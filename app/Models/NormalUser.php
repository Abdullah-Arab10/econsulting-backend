<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NormalUser extends Model
{

    protected $fillable = [
        'favorite_consultants',
        'user_id'
    ];
    protected $casts = [
        'favorite_consultants' => 'array'
    ];
    use HasFactory;
}
