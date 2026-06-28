<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use App\Mail\SyncNotificationMail;
use App\Mail\ManagerApprovedMail;
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
    ];

    /**
     * Send notification to web (database) and optionally to email if crucial.
     */
    public function send(User $user, string $title, string $message, string $type, ?string $link = null, $notifiable = null)
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
            $this->dispatchEmail($notification, $user);
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
     */
    protected function dispatchEmail(AppNotification $notification, User $user)
    {
        try {
            if ($notification->type === 'manager_approved') {
                // Determine action based on title
                $action = 'approved';
                if (str_contains($notification->title, 'Dicabut')) {
                    $action = 'revoked';
                } elseif (str_contains($notification->title, 'Ditolak') || str_contains($notification->title, 'Tidak Disetujui')) {
                    $action = 'rejected';
                }
                
                // Extract notes from message (we pass notes after double newline if any)
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
            } elseif ($notification->type === 'contract_requested') {
                $contract = $notification->notifiable;
                if ($contract) {
                    $contract->loadMissing(['unit.property', 'tenant', 'manager']);
                    Mail::to($user->email)->send(new \App\Mail\ContractRequestedMail($contract));
                } else {
                    throw new \Exception('Data kontrak terkait tidak ditemukan.');
                }
            } else {
                Mail::to($user->email)->send(new SyncNotificationMail($notification));
            }
            
            $notification->update([
                'email_status' => 'sent',
                'email_error' => null,
            ]);
            
            return true;
        } catch (\Throwable $e) {
            $notification->update([
                'email_status' => 'failed',
                'email_error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
}
