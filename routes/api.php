<?php

use App\Http\Controllers\ContactController;

Route::middleware(['web', 'throttle:6,1'])->group(function () {
    Route::post('/contact', [ContactController::class, 'submit']);
}); 