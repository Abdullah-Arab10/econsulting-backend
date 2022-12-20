<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'appointment_date',
        'appointment_start',
        'appointment_end',
        'client_id',
        'consultant_id'
    ];

     public function user(){
        return $this->belongsTo('App\User');
    }
}
