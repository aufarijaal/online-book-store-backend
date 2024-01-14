<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = "cart_items";

    protected $guarded = [
        'id'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
