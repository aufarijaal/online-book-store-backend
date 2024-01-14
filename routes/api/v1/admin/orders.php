<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('orders')->group(function () {
  Route::get('/', [\App\Http\Controllers\API\v1\Admin\OrderController::class, 'index'])->name('orders.index');

  Route::get('/{id}', [\App\Http\Controllers\API\v1\Admin\OrderController::class, 'show'])->name('orders.show');

  // Route::get('/{id}/edit', function (string $id) {
  //   $author = \App\Models\Author::find($id, ['id', 'name', 'dob', 'nationality']);

  //   return response()->json([
  //     'message' => 'OK',
  //     'data' => $author
  //   ]);
  // })->name('orders.edit');

  Route::post('/', [\App\Http\Controllers\API\v1\Admin\OrderController::class, 'store'])->name('orders.store');

  // Route::match(['put', 'patch'], '/{id}', function (Request $r) {
  //   \Illuminate\Support\Facades\DB::beginTransaction();

  //   $validated = $r->validate([
  //     'id' => 'required|numeric|integer|exists:authors,id',
  //     'name' => 'string|max:255|unique:authors,name',
  //     'dob' => 'string|max:255|date_format:Y-m-d',
  //     'nationality' => 'string|max:255',
  //   ]);

  //   \App\Models\Author::where('id', $validated['id'])->update([
  //     ...$r->only(['name', 'dob', 'nationality']),
  //     'slug' => \Illuminate\Support\Str::slug($r->input('name'))
  //   ]);
  //   \Illuminate\Support\Facades\DB::commit();

  //   return response()->json([
  //     'message' => 'OK'
  //   ]);
  // })->name('orders.update');

  // Route::delete('/', function (Request $r) {
  //   \Illuminate\Support\Facades\DB::beginTransaction();
  //   \App\Models\Author::whereIn('id', explode(',', $r->query('ids')))->delete();
  //   \Illuminate\Support\Facades\DB::commit();

  //   return response()->noContent();
  // })->name('orders.destroy');
});
