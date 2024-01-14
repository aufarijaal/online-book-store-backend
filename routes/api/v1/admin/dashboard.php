<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\Admin\DashboardController::class, 'index'])->name('dashboard.index');
});
