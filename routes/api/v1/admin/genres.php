<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('genres')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\Admin\GenreController::class, 'index'])->name('genres.index');
  Route::get('/{id}/edit', [\App\Http\Controllers\API\v1\Admin\GenreController::class, 'edit'])->name('genres.edit');
  Route::post('/', [\App\Http\Controllers\API\v1\Admin\GenreController::class, 'store'])->name('genres.store');
  Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\API\v1\Admin\GenreController::class, 'update'])->name('genres.update');
  Route::delete('/', [\App\Http\Controllers\API\v1\Admin\GenreController::class, 'destroy'])->name('genres.destroy');
});
