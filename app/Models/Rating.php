<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'rate',
        'client_id',
        'consultant_id'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
}
