<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultantController extends Controller
{
    //
    public function getAllConsultant()
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
            "civil_engineers" => $civilEngineers];
        return response()->json($consultantsList, 200);
    }

   public function getConsultantDetails($id){
        $consultant = User::query()
            ->join('consultants', 'users.id', '=', 'consultants.user_id')
            ->where('users.id',$id)
            ->get();

        return response()->json($consultant,200);


    }
}
