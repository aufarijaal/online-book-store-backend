<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('books')->group(function () {
  // Get books
  Route::get('/', [\App\Http\Controllers\API\v1\Admin\BookController::class, 'index'])->name('books.index');

  // Get a book data to edit
  Route::get('/{id}/edit', [\App\Http\Controllers\API\v1\Admin\BookController::class, 'edit'])->name('books.edit');

  // Create a book
  Route::post('/', [\App\Http\Controllers\API\v1\Admin\BookController::class, 'store'])->name('books.store');

  // Update a book
  Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\API\v1\Admin\BookController::class, 'update'])->name('books.update');

  Route::delete('/', [\App\Http\Controllers\API\v1\Admin\BookController::class, 'destroy'])->name('books.destroy');
});
