<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'avatar',
        'id_number', 'date_of_birth', 'address',
        'referral_code', 'manager_status', 'manager_notes',
        'bank_name', 'bank_account_number', 'bank_account_name',
        'notif_email', 'notif_due_date', 'user_code',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth'     => 'date',
        'notif_email'       => 'boolean',
        'notif_due_date'    => 'boolean',
    ];

    // ── Role helpers ──────────────────────────────────────────────
    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isManager(): bool    { return $this->role === 'manager'; }
    public function isTenant(): bool     { return $this->role === 'tenant'; }
    public function isApprovedManager(): bool
    {
        return $this->role === 'manager' && $this->manager_status === 'approved';
    }

    // ── Relationships: Manager ─────────────────────────────────────
    public function properties()
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    public function managedContracts()
    {
        return $this->hasMany(LeaseContract::class, 'manager_id');
    }

    public function managedInvoices()
    {
        return $this->hasMany(Invoice::class, 'manager_id');
    }

    // ── Relationships: Tenant ──────────────────────────────────────
    public function leaseContracts()
    {
        return $this->hasMany(LeaseContract::class, 'tenant_id');
    }

    public function activeContract()
    {
        return $this->hasOne(LeaseContract::class, 'tenant_id')
                    ->where('status', 'active')
                    ->latest();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tenant_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    public function facilityRequests()
    {
        return $this->hasMany(FacilityRequest::class, 'tenant_id');
    }

    public function emergencyReports()
    {
        return $this->hasMany(EmergencyReport::class, 'tenant_id');
    }

    // ── Notifications ──────────────────────────────────────────────
    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class, 'user_id')->orderByDesc('created_at');
    }

    public function unreadNotifications()
    {
        return $this->appNotifications()->where('is_read', false);
    }

    // ── Accessors ──────────────────────────────────────────────────
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4C74AF&color=fff&size=128';
    }

    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'superadmin' => 'Super Admin',
            'manager'    => 'Pengelola',
            'tenant'     => 'Penyewa',
            default      => $this->role,
        };
    }
}
