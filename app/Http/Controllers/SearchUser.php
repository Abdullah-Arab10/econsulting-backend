<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchUser extends Controller
{
    //
    public function Search(Request $request){
        $request ->validate([
            "username" =>"required|min:3"
        ]);
        $search = $request -> username;
        $users= User::query()->join('consultants', 'users.id', '=', 'consultants.user_id')
                ->where(function ($qs) use ($search){
            $qs -> orWhere('first_name','like',"%{$search}%")
                ->orWhere('last_name','like',"%{$search}%");
              })->get();


        return response() -> json($users,200);
     }
}
