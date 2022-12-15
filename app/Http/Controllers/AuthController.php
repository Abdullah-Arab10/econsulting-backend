<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            "firstName" => "required|string|min:3",
            "lastName" => "required|string|min:3",
            "email" => "required|string|unique:users|email",
            "password" => "required|string|min:6",
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images');
        };
        $user = User::create([
            "first_name" => $request->firstName,
            "last_name" => $request->lastName,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "address" => $request->address,
            "wallet"=>0,
            "image" => $imagePath,
            "role" => 2
        ]);
        $token = $user->createToken("Very Secret Strong Token")->plainTextToken;
        $respone = ["message" => "user has been added successfully", "user" => $user, "token" => $token];
        return response()->json($respone, 200);
    }

    public function registerAsConsultant(Request $request)
    {

        $rules = [
            "firstName" => "required|string|min:3",
            "lastName" => "required|string|min:3",
            "email" => "required|string|unique:users|email",
            "password" => "required|string|min:6",
            "skill" => "required",
            "shiftStart" => "required|date_format:H:i",
            "shiftEnd" => "required|date_format:H:i|after:shiftStart"
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
      // if ($request->hasFile('image')) {
         // $imagePath = $request->file('image')->store('images');
      // };
        $formatterdshiftStart = Carbon::createFromFormat('H:i', $request->shiftStart)->format('H:i:s');
        $formatterdshiftEnd = Carbon::createFromFormat('H:i', $request->shiftEnd)->format('H:i:s');
        $user = User::create([
            "first_name" => $request->firstName,
            "last_name" => $request->lastName,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role" => 1,
            "address" => $request->address,
           //"image" => $imagePath,
            "phone" => $request->phone
        ]);
        $consultant = Consultant::create([
            "user_id" => $user->id,
            "skill" => $request->skill,
            "bio" => $request->bio,
           
            "shiftStart" => $formatterdshiftStart,
            "shiftEnd" => $formatterdshiftEnd

        ]);
        $user->bio = $consultant->bio;
        $user->skill = $consultant->skill;
        
        $user->shiftStart = $consultant->shiftStart;
        $user->shiftEnd = $consultant->shiftEnd;
        $token = $user->createToken("Very Secret Strong Token")->plainTextToken;
        $respone = ["message" => "user has been added successfully", "user" => $user, "token" => $token];
        return response()->json($respone, 200);
    }

    public function login(Request $request)
    {
        $rules = [
            "email" => "required|email",
            "password" => "required|string|min:6"
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(["message" => "User is not found"], 400);
        }
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(["message" => "Password is not correct"], 400);
        }
        $token = $user->createToken("Very Secret Strong Token")->plainTextToken;
        $respone = ["message" => "user has been added successfully", "user" => $user, "token" => $token];
        return response()->json($respone, 200);
    }
}
