<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Illuminate\Support\Facades\Storage;

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

    public function rating(Request $request, $clintId, $consultantId)
    {

        $request->validate([
            'rate' => 'required'
        ]);
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
        return response()->json(200);
    }


    public function AvgRating($id)
    {
        $avg = Rating::query()->where('ratings.consultant_id', $id)->avg('ratings.rate');
        $formatted_number = round($avg, 2);
        return $formatted_number;
    }
}
