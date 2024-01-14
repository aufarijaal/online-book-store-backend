<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('account')->middleware(['auth:sanctum'])->group(function () {
  Route::get('/profile', [\App\Http\Controllers\API\v1\AccountController::class, 'getProfile'])->name('account.profile');
  Route::match(['put', 'patch'], '/update-name', [\App\Http\Controllers\API\v1\AccountController::class, 'updateName'])->name('account.update_name');
  Route::match(['put', 'patch'], '/update-email', [\App\Http\Controllers\API\v1\AccountController::class, 'updateEmail'])->name('account.update_email');
  Route::match(['put', 'patch'], '/update-password', [\App\Http\Controllers\API\v1\AccountController::class, 'updatePassword'])->name('account.update_password');
});
