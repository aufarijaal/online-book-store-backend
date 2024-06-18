<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
