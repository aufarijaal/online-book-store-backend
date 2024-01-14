<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('addresses')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\Admin\AddressController::class, 'index'])->name('addresses.index');
  Route::post('/', [\App\Http\Controllers\API\v1\Admin\AddressController::class, 'store'])->name('addresses.store');
  Route::get(
    '/{id}/edit',
    [\App\Http\Controllers\API\v1\Admin\AddressController::class, 'edit']
  )->name('addresses.edit');
  Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\API\v1\Admin\AddressController::class, 'update'])->name('addresses.update');
  Route::delete('/', [\App\Http\Controllers\API\v1\Admin\AddressController::class, 'destroy'])->name('addresses.destroy');
});
