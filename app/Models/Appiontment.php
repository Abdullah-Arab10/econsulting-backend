<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appiontment extends Model
{
    use HasFactory;
    protected $fillable=[
        'appointment_start',
        'appointment_end'
    ];
}
