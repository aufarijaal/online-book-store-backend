<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Regular User Routes
Route::prefix('v1')->group(function () {
    foreach (glob(__DIR__ . "/api/v1/*.php") as $file) {
        require $file;
    }
});

// Admin Routes
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('v1/admin')->group(function () {
    foreach (glob(__DIR__ . "/api/v1/admin/*.php") as $file) {
        require $file;
    }
});
