<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('authors')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\AuthorController::class, 'index'])->name('authors.index');
  Route::get('/{authorSlug}', [\App\Http\Controllers\API\v1\AuthorController::class, 'show'])->name('authors.show');
});
