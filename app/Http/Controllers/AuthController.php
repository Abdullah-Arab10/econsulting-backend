<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
            "role" => "required|integer|between:0,10"
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
            "role" => $request->role,
            "address" => $request->address,
            "image" => $imagePath
        ]);
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
