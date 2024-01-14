<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('authors')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\Admin\AuthorController::class, 'index'])->name('authors.index');
  Route::get('/{id}/edit', [\App\Http\Controllers\API\v1\Admin\AuthorController::class, 'edit'])->name('authors.edit');
  Route::post('/', [\App\Http\Controllers\API\v1\Admin\AuthorController::class, 'store'])->name('authors.store');
  Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\API\v1\Admin\AuthorController::class, 'update'])->name('authors.update');
  Route::delete('/', [\App\Http\Controllers\API\v1\Admin\AuthorController::class, 'destroy'])->name('authors.destroy');
});
