<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShilappOrderApiController;

Route::middleware('shilapp.signature')
    ->prefix('shilapp')
    ->group(function () {
        Route::get('/orders', [ShilappOrderApiController::class, 'orders']);
        Route::get('/order/{orderId}', [ShilappOrderApiController::class, 'order']);
        Route::post('/order_update', [ShilappOrderApiController::class, 'order_update']);
    });