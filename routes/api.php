<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShilappOrderApiController;

Route::middleware('shilapp.signature')
    ->prefix('shilapp')
    ->group(function () {
        Route::get('/orders', [ShilappOrderApiController::class, 'index']);
        Route::get('/orders/{order}', [ShilappOrderApiController::class, 'show']);
    });