<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $table = 'users';

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
