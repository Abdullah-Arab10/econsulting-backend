<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    //
    public function getAllConsultant (){
      $consultantsList = User::where('role',1)->get();
        return response()->json($consultantsList,200);
    }
}
