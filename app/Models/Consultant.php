<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skill',
        'bio',
        'shiftStart',
        'shiftEnd'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
}
