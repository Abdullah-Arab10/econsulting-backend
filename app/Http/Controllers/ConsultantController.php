<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConsultantController extends Controller
{
    //
    public function getAllConsultants()
    {
        $consultants = User::query()
            ->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->get();
        foreach ($consultants as $consultant) {
            if ($consultant->image) {
                $imagePath = $consultant->image;
                $imagePath = substr($imagePath, strpos($imagePath, "images"));
                $path = Storage::path($imagePath);
                $imageBase64 = base64_encode(file_get_contents($path));
                $consultant->image = $imageBase64;
            }
        }
        $doctors = [];
        $dentists = [];
        $therapists = [];
        $lawyers = [];
        $economists = [];
        $civilEngineers = [];
        $softwareEngineers = [];
        foreach ($consultants as $consultant) {
            if ($consultant->skill == 0) array_push($doctors, $consultant);
            if ($consultant->skill == 1) array_push($dentists, $consultant);
            if ($consultant->skill == 2) array_push($therapists, $consultant);
            if ($consultant->skill == 3) array_push($lawyers, $consultant);
            if ($consultant->skill == 4) array_push($economists, $consultant);
            if ($consultant->skill == 5) array_push($softwareEngineers, $consultant);
            if ($consultant->skill == 6) array_push($civilEngineers, $consultant);
        }
        $consultantsList = [
            "doctors" => $doctors,
            "dentists" => $dentists,
            "therapists" => $therapists,
            "lawyers" => $lawyers,
            "economists" => $economists,
            "software_engineers" => $softwareEngineers,
            "civil_engineers" => $civilEngineers
        ];
        return response()->json($consultantsList, 200);
    }

    public function getConsultantDetails($id)
    {
        $consultant = User::query()
            ->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->where('users.id', $id)
            ->get();
        $consultant = $consultant[0];
        if ($consultant->image) {
            $imagePath = $consultant->image;
            $imagePath = substr($imagePath, strpos($imagePath, "images"));
            $path = Storage::path($imagePath);
            $imageBase64 = base64_encode(file_get_contents($path));
            $consultant->image = $imageBase64;
        }
        return response()->json($consultant, 200);
    }


    public function Search(Request $request)
    {
        $request->validate([
            "username" => "required|min:3"
        ]);
        $search = $request->username;
        $users = User::query()->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->where(function ($qs) use ($search) {
                $qs->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            })->get();


        return response()->json($users, 200);
    }
}
