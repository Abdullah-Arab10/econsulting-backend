<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConsultantController extends Controller
{
    //
    public function getAllConsultants()
    {
        $consultants = User::query()
            ->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->get();
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
        return response()->json(["data" => $consultantsList], 200);
    }



    public function getConsultantDetails($id)
    {
        $consultant = User::query()
            ->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->where('users.id', $id)
            ->get();
        return response()->json(["data" => $consultant], 200);
    }




    public function Search(Request $request)
    {
        $rules = [
            'username' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $search = $request->username;
        $users = User::query()->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->where(function ($qs) use ($search) {
                $qs->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            })->get();


        return response()->json($users, 200);
    }

    public function rating(Request $request)
    {

        $rules = [
            'rate' => 'required',
            'consultantId' => 'required',
            'clientId' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $clintId = $request->clientId;
        $consultantId = $request->consultantId;
        $user = Rating::where(function ($q) use ($clintId, $consultantId) {
            $q->where('client_id', $clintId)
                ->where('consultant_id', $consultantId);
        })->first();

        if ($user) {
            $user->rate = $request->rate;
            $user->save();
        } else {
            $rate = Rating::create([
                "rate" => $request->rate,
                "client_id" => $clintId,
                "consultant_id" => $consultantId
            ]);
        }
        $rate = $this->AvgRating($consultantId);
        $avgrating = Consultant::where('consultants.id', $consultantId)->first();
        $avgrating->AvgRating =  $rate;
        $avgrating->save();
        return response()->json(["message" => "Rating added successfully"], 200);
    }


    public function AvgRating($id)
    {
        $avg = Rating::query()->where('ratings.consultant_id', $id)->avg('ratings.rate');
        $formatted_number = round($avg, 2);
        return $formatted_number;
    }
}
