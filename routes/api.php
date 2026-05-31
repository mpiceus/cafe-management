<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

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

Route::post('sepay/webhook', [PaymentController::class, 'webhook'])
    ->name('sepay.webhook');

// API endpoint for polling payment status from customer display
Route::get('payment-status/{hoaDon}', [PaymentController::class, 'status'])
    ->name('api.payment.status');
