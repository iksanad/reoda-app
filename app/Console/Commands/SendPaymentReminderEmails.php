<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Notification;
use App\Mail\PaymentReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendPaymentReminderEmails extends Command
{
    protected $signature   = 'reoda:send-payment-reminders';
    protected $description = 'Send payment reminder emails for invoices due today and late invoices nearing tolerance deadline';

    public function handle()
    {
        $today = Carbon::today();

        // 0. Invoice jatuh tempo dalam 3 Hari dan 1 Hari (H-3 dan H-1)
        $daysToRemind = [3, 1];
        foreach ($daysToRemind as $days) {
            $targetDate = $today->copy()->addDays($days);
            $upcoming = Invoice::whereIn('status', ['unpaid', 'pending'])
                ->whereDate('due_date', $targetDate)
                ->with(['leaseContract.tenant', 'leaseContract.unit.property'])
                ->get();

            foreach ($upcoming as $invoice) {
                $tenant   = $invoice->leaseContract->tenant;
                $contract = $invoice->leaseContract;
                $unit     = $contract->unit;
                $property = $unit->property ?? null;

                if (!$tenant || !$tenant->email) continue;

                // In-app notification
                Notification::create([
                    'user_id' => $tenant->id,
                    'type'    => 'payment_upcoming',
                    'title'   => '🔔 Pengingat: Tagihan Jatuh Tempo dalam ' . $days . ' Hari',
                    'message' => 'Tagihan sewa ' . ($property->name ?? '') . ' unit ' . ($unit->unit_code ?? '') .
                                 ' sebesar Rp ' . number_format($invoice->amount, 0, ',', '.') .
                                 ' akan jatuh tempo pada ' . $targetDate->format('d M Y') . '. Mohon persiapkan pembayaran.',
                ]);

                // Send email
                if ($tenant->email) {
                    try {
                        Mail::to($tenant->email)->send(new PaymentReminderMail($invoice, $contract, 'upcoming_' . $days));
                    } catch (\Exception $e) {
                        $this->warn("Email gagal ke {$tenant->email}: " . $e->getMessage());
                    }
                }
            }
        }

        // 1. Invoice jatuh tempo HARI INI (unpaid)
        $dueToday = Invoice::whereIn('status', ['unpaid', 'pending'])
            ->whereDate('due_date', $today)
            ->with(['leaseContract.tenant', 'leaseContract.unit.property'])
            ->get();

        foreach ($dueToday as $invoice) {
            $tenant   = $invoice->leaseContract->tenant;
            $contract = $invoice->leaseContract;
            $unit     = $contract->unit;
            $property = $unit->property ?? null;

            if (!$tenant || !$tenant->email) continue;

            // In-app notification
            Notification::create([
                'user_id' => $tenant->id,
                'type'    => 'payment_due',
                'title'   => '🔔 Tagihan Jatuh Tempo Hari Ini',
                'message' => 'Tagihan sewa ' . ($property->name ?? '') . ' unit ' . ($unit->unit_code ?? '') .
                             ' sebesar Rp ' . number_format($invoice->amount, 0, ',', '.') .
                             ' jatuh tempo hari ini. Segera lakukan pembayaran.',
            ]);

            // Send email
            if ($tenant->email) {
                try {
                    Mail::to($tenant->email)->send(new PaymentReminderMail($invoice, $contract, 'due_today'));
                } catch (\Exception $e) {
                    $this->warn("Email gagal ke {$tenant->email}: " . $e->getMessage());
                }
            }
        }

        // 2. Invoice yang SUDAH melewati jatuh tempo, H-1 sebelum batas toleransi berakhir
        $overdue = Invoice::whereIn('status', ['unpaid', 'pending'])
            ->where('due_date', '<', $today)
            ->with(['leaseContract.tenant', 'leaseContract.unit.property'])
            ->get();

        foreach ($overdue as $invoice) {
            $contract      = $invoice->leaseContract;
            $toleranceDays = $contract->tolerance_days ?? 7;
            $deadline      = Carbon::parse($invoice->due_date)->addDays($toleranceDays);
            $daysLeft      = $today->diffInDays($deadline, false);

            // Send warning when 1 day left
            if ($daysLeft === 1) {
                $tenant   = $contract->tenant;
                $unit     = $contract->unit;
                $property = $unit->property ?? null;

                if (!$tenant) continue;

                Notification::create([
                    'user_id' => $tenant->id,
                    'type'    => 'payment_overdue_warning',
                    'title'   => '⚠️ Peringatan: 1 Hari Tersisa Sebelum Kontrak Berakhir',
                    'message' => 'Tagihan sewa ' . ($property->name ?? '') . ' belum dibayar. Batas toleransi berakhir besok (' .
                                 $deadline->format('d M Y') . '). Jika tidak dibayar, kontrak Anda akan dihentikan otomatis.',
                ]);

                // Send email
                if ($tenant->email) {
                    try {
                        Mail::to($tenant->email)->send(new PaymentReminderMail($invoice, $contract, 'overdue_warning', $deadline));
                    } catch (\Exception $e) {
                        $this->warn("Email gagal ke {$tenant->email}: " . $e->getMessage());
                    }
                }
                $this->info("Warning sent to {$tenant->email} — tolerance deadline tomorrow.");
            }
        }

        $this->info("Payment reminders processed: {$dueToday->count()} due today, {$overdue->count()} overdue checked.");
        return Command::SUCCESS;
    }
}
