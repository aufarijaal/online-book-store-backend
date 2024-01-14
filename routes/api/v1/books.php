<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('books')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\BookController::class, 'index'])->name('books.index');
  Route::get('/detail/{slug}', [\App\Http\Controllers\API\v1\BookController::class, 'getOneBySlug'])->name('books.get_one_by_slug');
  Route::get('/search', [\App\Http\Controllers\API\v1\BookController::class, 'search'])->name('books.search');
});
