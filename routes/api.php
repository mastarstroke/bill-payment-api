<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\BillPaymentController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Wallet System
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::post('/wallet/fund', [WalletController::class, 'fund']);
    Route::post('/wallet/deduct', [WalletController::class, 'deduct']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

    // Airtime Purchase
    Route::post('/bill/airtime', [BillPaymentController::class, 'purchaseAirtime']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
