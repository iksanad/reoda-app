<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ManagerApprovalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = User::where('role', 'manager')->latest();

        if ($status !== 'all') {
            $query->where('manager_status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $managers = $query->paginate(15)->appends(['status' => $status, 'search' => $search]);

        $counts = [
            'all'      => User::where('role', 'manager')->count(),
            'pending'  => User::where('role', 'manager')->where('manager_status', 'pending')->count(),
            'approved' => User::where('role', 'manager')->where('manager_status', 'approved')->count(),
            'rejected' => User::where('role', 'manager')->where('manager_status', 'rejected')->count(),
        ];

        return view('superadmin.managers.index', compact('managers', 'status', 'counts', 'search'));
    }

    public function approve(Request $request, User $manager)
    {
        abort_if($manager->role !== 'manager', 404);

        $manager->update([
            'manager_status' => 'approved',
            'manager_notes'  => $request->notes ?? null,
        ]);

        // Kirim email notifikasi ke pengelola menggunakan NotificationService
        $title = 'Selamat! Anda Diterima 🎉';
        $message = "Selamat! Tim REODA telah menyetujui pendaftaran Anda sebagai Pengelola (Manager).";
        if ($request->notes) {
            $message .= "\n\nCatatan: " . $request->notes;
        }

        $this->notificationService->send(
            $manager, 
            $title, 
            $message, 
            'manager_approved', 
            '/manager/dashboard',
            $manager
        );

        return redirect()->back()->with('success', "Pengelola {$manager->name} berhasil disetujui dan notifikasi telah dikirim.");
    }

    public function reject(Request $request, User $manager)
    {
        abort_if($manager->role !== 'manager', 404);

        $request->validate(['notes' => 'required|string|min:5']);
        
        $isRevocation = $manager->manager_status === 'approved';

        $manager->update([
            'manager_status' => 'rejected',
            'manager_notes'  => $request->notes,
        ]);

        // Kirim email notifikasi ke pengelola menggunakan NotificationService
        $action = $isRevocation ? 'revoked' : 'rejected';
        
        $title = $isRevocation ? 'Hak Akses Dicabut ⚠️' : 'Pendaftaran Tidak Disetujui ❌';
        $message = $isRevocation 
            ? "Hak akses Anda sebagai Pengelola di REODA telah dicabut oleh sistem kami." 
            : "Maaf, pendaftaran Anda belum dapat kami setujui.";
        
        if ($request->notes) {
            $message .= "\n\nAlasan: " . $request->notes;
        }

        $this->notificationService->send(
            $manager, 
            $title, 
            $message, 
            'manager_approved', 
            null,
            $manager
        );

        $msg = $isRevocation ? "Hak akses pengelola {$manager->name} telah dicabut." : "Pengelola {$manager->name} telah ditolak.";
        return redirect()->back()->with('success', $msg . " Notifikasi telah dikirim.");
    }
}
