<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    use HasFactory;

    protected $hidden =[
     'wallet'
    ];

    protected $fillable = [
        'user_id',
        'skill',
        'bio',
        'shiftStart',
        'shiftEnd',
        'appointment_cost'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }


    public $timestamps = false;
}
