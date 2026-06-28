<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use App\Mail\SyncNotificationMail;
use App\Mail\ManagerApprovedMail;
use App\Mail\ContractApprovedMail;
use App\Mail\ContractRequestedMail;
use App\Mail\PaymentApprovedMail;
use App\Mail\ManagerPaymentReceivedMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class NotificationService
{
    /**
     * Tipe notifikasi yang krusial dan wajib dikirim ke email.
     */
    protected $crucialTypes = [
        'payment_due',
        'payment_received',
        'payment_approved',
        'payment_rejected',
        'contract_expiring',
        'contract_renewed',
        'contract_terminated',
        'manager_approved',
        'facility_request',
        'emergency_report',
        'contract_requested',
        'contract_approved',
        'contract_rejected',
        'payment_manager_received',
    ];

    /**
     * Send notification to web (database) and optionally to email if crucial.
     *
     * @param bool $noRetry  Pass true in time-sensitive contexts (e.g. webhooks)
     *                       to skip the sleep-based retry and return immediately.
     */
    public function send(User $user, string $title, string $message, string $type, ?string $link = null, $notifiable = null, bool $noRetry = false)
    {
        $shouldSendEmail = in_array($type, $this->crucialTypes);

        $notification = AppNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'email_status' => $shouldSendEmail ? 'pending' : 'none',
        ]);

        if ($shouldSendEmail) {
            $this->dispatchEmail($notification, $user, $noRetry);
        }

        return $notification;
    }

    /**
     * Resend an existing failed or pending notification email.
     */
    public function resendEmail(AppNotification $notification)
    {
        if ($notification->email_status === 'none') {
            return false;
        }

        $user = $notification->user;
        if (!$user) {
            return false;
        }

        return $this->dispatchEmail($notification, $user);
    }

    /**
     * Dispatch email logic and update DB status.
     * Attempts up to 2 times with a 2-second delay between attempts.
     *
     * @param bool $noRetry  Skip the sleep-based retry (for time-sensitive callers like webhooks).
     */
    protected function dispatchEmail(AppNotification $notification, User $user, bool $noRetry = false)
    {
        $lastError = null;
        $attempts  = $noRetry ? 1 : 2;

        for ($i = 1; $i <= $attempts; $i++) {
            try {
                $this->sendMailable($notification, $user);

                $notification->update([
                    'email_status' => 'sent',
                    'email_error'  => null,
                ]);

                return true;
            } catch (\Throwable $e) {
                $lastError = $e;

                // Wait before retrying (skip sleep on last attempt or when noRetry=true)
                if ($i < $attempts && !$noRetry) {
                    sleep(2);
                }
            }
        }

        // All attempts failed — mark as failed so Superadmin can resend
        $notification->update([
            'email_status' => 'failed',
            'email_error'  => $lastError?->getMessage(),
        ]);

        return false;
    }

    /**
     * Select and send the correct Mailable for the notification type.
     */
    protected function sendMailable(AppNotification $notification, User $user): void
    {
        switch ($notification->type) {
            case 'manager_approved':
                $action = 'approved';
                if (str_contains($notification->title, 'Dicabut')) {
                    $action = 'revoked';
                } elseif (str_contains($notification->title, 'Ditolak') || str_contains($notification->title, 'Tidak Disetujui')) {
                    $action = 'rejected';
                }
                $notes = null;
                $parts = explode("\n\nCatatan: ", $notification->message);
                if (count($parts) > 1) {
                    $notes = $parts[1];
                } else {
                    $parts = explode("\n\nAlasan: ", $notification->message);
                    if (count($parts) > 1) {
                        $notes = $parts[1];
                    }
                }
                Mail::to($user->email)->send(new ManagerApprovedMail($user, $action, $notes));
                break;

            case 'contract_requested':
                $contract = $notification->notifiable;
                if (!$contract) {
                    throw new \Exception('Data kontrak terkait tidak ditemukan.');
                }
                $contract->loadMissing(['unit.property', 'tenant', 'manager']);
                Mail::to($user->email)->send(new ContractRequestedMail($contract));
                break;

            case 'contract_approved':
                $contract = $notification->notifiable;
                if (!$contract) {
                    throw new \Exception('Data kontrak terkait tidak ditemukan.');
                }
                $contract->loadMissing(['unit.property', 'tenant', 'manager']);
                Mail::to($user->email)->send(new ContractApprovedMail($contract, true));
                break;

            case 'contract_rejected':
                $contract = $notification->notifiable;
                if (!$contract) {
                    throw new \Exception('Data kontrak terkait tidak ditemukan.');
                }
                $contract->loadMissing(['unit.property', 'tenant', 'manager']);
                Mail::to($user->email)->send(new ContractApprovedMail($contract, false));
                break;

            case 'payment_approved':
                $payment = $notification->notifiable;
                if (!$payment) {
                    throw new \Exception('Data pembayaran terkait tidak ditemukan.');
                }
                $payment->loadMissing(['invoice.leaseContract.unit.property', 'invoice.leaseContract.tenant', 'tenant']);
                Mail::to($user->email)->send(new PaymentApprovedMail($payment, 'approved'));
                break;

            case 'payment_rejected':
                $payment = $notification->notifiable;
                if (!$payment) {
                    throw new \Exception('Data pembayaran terkait tidak ditemukan.');
                }
                $payment->loadMissing(['invoice.leaseContract.unit.property', 'invoice.leaseContract.tenant', 'tenant']);
                // Extract rejection reason from message if any
                $notes = null;
                $parts = explode('Alasan: ', $notification->message);
                if (count($parts) > 1) {
                    $notes = trim($parts[1]);
                }
                Mail::to($user->email)->send(new PaymentApprovedMail($payment, 'rejected', $notes));
                break;

            case 'payment_received':
            case 'payment_manager_received':
                $payment = $notification->notifiable;
                if (!$payment) {
                    throw new \Exception('Data pembayaran terkait tidak ditemukan.');
                }
                $payment->loadMissing(['invoice.leaseContract.unit.property', 'invoice.leaseContract.tenant', 'tenant']);
                if ($notification->type === 'payment_manager_received') {
                    Mail::to($user->email)->send(new ManagerPaymentReceivedMail($payment));
                } else {
                    Mail::to($user->email)->send(new PaymentApprovedMail($payment, 'approved'));
                }
                break;

            default:
                Mail::to($user->email)->send(new SyncNotificationMail($notification));
                break;
        }
    }
}

