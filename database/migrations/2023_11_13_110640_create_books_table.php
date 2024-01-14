<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('genre_id');
            $table->string('title');
            $table->date('published_date');
            $table->decimal('price', 20, 2);
            $table->integer('stock_qty');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('author_id')->references('id')->on('authors');
            $table->foreign('genre_id')->references('id')->on('genres');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
