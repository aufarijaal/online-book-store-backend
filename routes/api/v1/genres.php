<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('genres')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\GenreController::class, 'index'])->name('genres.index');
  Route::get('/get-books-by-genre-slug/{genreSlug}', [\App\Http\Controllers\API\v1\GenreController::class, 'getBookByGenre'])->name('genres.get_book_by_genre');
});
