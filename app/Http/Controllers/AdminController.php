<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    //
    public function addMoneyToWallet(Request $request){

        $request->validate([

            "email" => "required|email",
            "cash" =>"required"
        ]);

        $user = User::where('email', $request->email)->first()->id;
        if(!$user){
            return response()->json(["message" => "User is not found"], 400);
        }
        $consultant = Consultant :: where('user_id' , $user) -> first();
        if(!$consultant){
            return response()->json(["message" => "User is not found"], 400);
        }

        $cash = $request -> cash;
       $consultant -> wallet += $cash  ;
       $consultant ->save();
 
            return response()->json([
                'status' => 1,
                'message' => "cash has been added"
            ]);
        
    

    }
}
