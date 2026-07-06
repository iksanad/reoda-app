<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\MidtransWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('api.midtrans.webhook');

// Scheduler endpoint — dipanggil oleh cron-job.org setiap jam
// Dilindungi token rahasia, tidak perlu auth session
Route::get('/scheduler/run/{token}', function (string $token) {
    if ($token !== config('app.scheduler_token')) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    \Illuminate\Support\Facades\Artisan::call('schedule:run');
    return response()->json(['message' => 'OK', 'executed_at' => now()->toISOString()], 200);
})->name('api.scheduler.run');

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){

    // PROPERTY
    Route::post('/properties',[PropertyController::class,'store']);
    Route::get('/properties',[PropertyController::class,'index']);
    Route::get('/properties/{id}',[PropertyController::class,'show']);

    // RENTAL
    Route::post('/rentals',[RentalController::class,'store']);

    // INVOICE
    Route::post('/invoices/{rental_id}',[InvoiceController::class,'create']);
    Route::get('/invoices',[InvoiceController::class,'index']);

    // PAYMENT
    Route::post('/payments',[PaymentController::class,'upload']);
});