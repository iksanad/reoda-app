<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = AppNotification::where('email_status', '!=', 'none')
                    ->with('user:id,name,email')
                    ->latest();

        if ($status !== 'all') {
            $query->where('email_status', $status);
        }

        $logs = $query->paginate(20)->appends(['status' => $status]);

        $counts = [
            'all' => AppNotification::where('email_status', '!=', 'none')->count(),
            'pending' => AppNotification::where('email_status', 'pending')->count(),
            'sent' => AppNotification::where('email_status', 'sent')->count(),
            'failed' => AppNotification::where('email_status', 'failed')->count(),
        ];

        return view('superadmin.email-logs.index', compact('logs', 'status', 'counts'));
    }

    public function resend(AppNotification $log)
    {
        if ($log->email_status === 'none') {
            return back()->with('error', 'Notifikasi ini tidak dikonfigurasi untuk email.');
        }

        $success = $this->notificationService->resendEmail($log);

        if ($success) {
            return back()->with('success', 'Email berhasil dikirim ulang ke ' . optional($log->user)->email);
        } else {
            return back()->with('error', 'Gagal mengirim ulang email. Silakan cek pengaturan SMTP.');
        }
    }
}
