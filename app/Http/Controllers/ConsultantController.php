<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ConsultantController extends Controller
{
    //
    public function getAllConsultant (){
      $consultantsList = User::where('role',1)->get();
        $users = DB::table('users')
            ->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->get();
        return response()->json($users ,200);
    }
}
