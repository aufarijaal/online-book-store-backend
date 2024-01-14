<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('addresses')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\AddressController::class, 'index'])->name('addresses.index');
  Route::post('/', [\App\Http\Controllers\API\v1\AddressController::class, 'store'])->name('addresses.store');
  // Route::get(
  //   '/{id}/edit',
  //   [\App\Http\Controllers\API\v1\AddressController::class, 'edit']
  // )->name('addresses.edit');
  Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\API\v1\AddressController::class, 'update'])->name('addresses.update');
  Route::delete('/{id}', [\App\Http\Controllers\API\v1\AddressController::class, 'destroy'])->name('addresses.destroy');
});
