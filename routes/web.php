<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Property Compare (public)
Route::get('/compare', [\App\Http\Controllers\CompareController::class, 'index'])->name('compare.index');

// Public Explore Market (no login required)
Route::get('/explore', [\App\Http\Controllers\ExplorePublicController::class, 'index'])->name('explore.public');

// Public Property Detail (via QR Code or Explore)
Route::get('/property/{property_code}', [\App\Http\Controllers\PublicPropertyController::class, 'show'])->name('property.public.show');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');

// General Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
});

// Superadmin Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Superadmin\DashboardController::class, 'index'])->name('dashboard');

    // Manager Approval
    Route::get('/managers', [\App\Http\Controllers\Superadmin\ManagerApprovalController::class, 'index'])->name('managers.index');
    Route::post('/managers/{manager}/approve', [\App\Http\Controllers\Superadmin\ManagerApprovalController::class, 'approve'])->name('managers.approve');
    Route::post('/managers/{manager}/reject', [\App\Http\Controllers\Superadmin\ManagerApprovalController::class, 'reject'])->name('managers.reject');

    // Global Settings
    Route::get('/settings', [\App\Http\Controllers\Superadmin\GlobalSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Superadmin\GlobalSettingController::class, 'update'])->name('settings.update');

    // Email Logs
    Route::get('/email-logs', [\App\Http\Controllers\Superadmin\EmailLogController::class, 'index'])->name('email-logs.index');
    Route::post('/email-logs/{log}/resend', [\App\Http\Controllers\Superadmin\EmailLogController::class, 'resend'])->name('email-logs.resend');

    // Withdrawals
    Route::get('/withdrawals', [\App\Http\Controllers\Superadmin\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals/{id}/approve', [\App\Http\Controllers\Superadmin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [\App\Http\Controllers\Superadmin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');
});

// Manager Routes
Route::middleware(['auth', 'role:manager', 'manager.terms'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/waiting', function () {
        return view('manager.waiting');
    })->name('waiting');

    // Terms & Conditions
    Route::get('/terms', [\App\Http\Controllers\Manager\TermsController::class, 'show'])->name('terms.show')->withoutMiddleware('manager.terms');
    Route::post('/terms/accept', [\App\Http\Controllers\Manager\TermsController::class, 'accept'])->name('terms.accept')->withoutMiddleware('manager.terms');

    Route::get('/properties/export', [\App\Http\Controllers\Manager\PropertyController::class, 'export'])->name('properties.export');
    Route::resource('properties', \App\Http\Controllers\Manager\PropertyController::class);
    Route::resource('properties.units', \App\Http\Controllers\Manager\UnitController::class)->shallow();

    // Data Penyewa
    Route::get('/tenants/export', [\App\Http\Controllers\Manager\TenantController::class, 'export'])->name('tenants.export');
    Route::get('/tenants', [\App\Http\Controllers\Manager\TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/{tenant}', [\App\Http\Controllers\Manager\TenantController::class, 'show'])->name('tenants.show');

    // Pembayaran
    Route::get('/payments/export', [\App\Http\Controllers\Manager\PaymentController::class, 'export'])->name('payments.export');
    Route::get('/payments', [\App\Http\Controllers\Manager\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [\App\Http\Controllers\Manager\PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/approve', [\App\Http\Controllers\Manager\PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Manager\PaymentController::class, 'reject'])->name('payments.reject');

    // Kontrak Sewa
    Route::get('/contracts/export', [\App\Http\Controllers\Manager\ContractController::class, 'export'])->name('contracts.export');
    Route::get('/contracts', [\App\Http\Controllers\Manager\ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/create', [\App\Http\Controllers\Manager\ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [\App\Http\Controllers\Manager\ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}', [\App\Http\Controllers\Manager\ContractController::class, 'show'])->name('contracts.show');
    Route::post('/contracts/{contract}/terminate', [\App\Http\Controllers\Manager\ContractController::class, 'terminate'])->name('contracts.terminate');
    Route::post('/contracts/{contract}/approve-request', [\App\Http\Controllers\Manager\ContractController::class, 'approveRequest'])->name('contracts.approve-request');
    Route::post('/contracts/{contract}/reject-request', [\App\Http\Controllers\Manager\ContractController::class, 'rejectRequest'])->name('contracts.reject-request');
    Route::post('/contracts/{contract}/invoices', [\App\Http\Controllers\Manager\ContractController::class, 'storeInvoice'])->name('contracts.invoices.store');

    // Laporan
    Route::get('/reports', [\App\Http\Controllers\Manager\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Manager\ReportController::class, 'export'])->name('reports.export');

    // Profil & Pengaturan
    Route::get('/profile', [\App\Http\Controllers\Manager\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Manager\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [\App\Http\Controllers\Manager\SettingsController::class, 'index'])->name('settings.index');

    // Saldo & Dompet
    Route::get('/wallet', [\App\Http\Controllers\Manager\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/withdraw', [\App\Http\Controllers\Manager\WalletController::class, 'withdraw'])->name('wallet.withdraw');

    // Explore Market
    Route::get('/explore', [\App\Http\Controllers\Tenant\ExploreController::class, 'index'])->name('explore.index');
    Route::get('/explore/{property}', [\App\Http\Controllers\Tenant\ExploreController::class, 'show'])->name('explore.show');
});

// Tenant Routes
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('dashboard');

    // Explore Market
    Route::get('/explore', [\App\Http\Controllers\Tenant\ExploreController::class, 'index'])->name('explore.index');
    Route::get('/explore/{property}', [\App\Http\Controllers\Tenant\ExploreController::class, 'show'])->name('explore.show');

    // Transaksi & Tagihan
    Route::get('/transactions', [\App\Http\Controllers\Tenant\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{invoice}', [\App\Http\Controllers\Tenant\TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{invoice}/pay', [\App\Http\Controllers\Tenant\TransactionController::class, 'pay'])->name('transactions.pay');

    // Profil & Pengaturan
    Route::get('/profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [\App\Http\Controllers\Tenant\SettingsController::class, 'index'])->name('settings.index');

    // Detail Kontrak
    Route::get('/contracts', [\App\Http\Controllers\Tenant\ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contract/{contract?}', [\App\Http\Controllers\Tenant\ContractController::class, 'show'])->name('contract.show');
    Route::post('/contract/{contract}/approve', [\App\Http\Controllers\Tenant\ContractController::class, 'approve'])->name('contract.approve');
    Route::post('/contract/{contract}/reject', [\App\Http\Controllers\Tenant\ContractController::class, 'reject'])->name('contract.reject');

    // Pengajuan Kontrak Baru oleh Penyewa
    Route::get('/request-contract/{property_code}', [\App\Http\Controllers\Tenant\ContractRequestController::class, 'show'])->name('contract.request');
    Route::post('/request-contract/{property_code}', [\App\Http\Controllers\Tenant\ContractRequestController::class, 'store'])->name('contract.request.store');

    // Layanan
    Route::get('/services', [\App\Http\Controllers\Tenant\ServiceRequestController::class, 'index'])->name('services.index');
    Route::post('/services/facility', [\App\Http\Controllers\Tenant\ServiceRequestController::class, 'storeFacility'])->name('services.facility');
    Route::post('/services/emergency', [\App\Http\Controllers\Tenant\ServiceRequestController::class, 'storeEmergency'])->name('services.emergency');
    Route::post('/services/contract', [\App\Http\Controllers\Tenant\ServiceRequestController::class, 'storeContractRequest'])->name('services.contract');
});

// Midtrans Webhook (no CSRF, no auth)
Route::post('/midtrans/webhook', [\App\Http\Controllers\MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');