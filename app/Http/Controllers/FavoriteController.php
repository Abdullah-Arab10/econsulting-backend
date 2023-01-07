<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Consultant;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\countOf;

class FavoriteController extends Controller
{
    //
    public function addFavorite(Request $request)
    {
        $rules = [
            "add" => "required|boolean",
            "clientid" => "required",
            "consultantid" => "required"
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $add = $request->add;
        $clintId = $request->clientid;
        $consultantId = $request->consultantid;

        if ($add == false) {
            $user = Favorite::where(function ($q) use ($clintId, $consultantId) {
                $q->where('client_id', $clintId)
                    ->where('consultant_id', $consultantId);
            })->delete();
            return response()->json(["message" => "consultant removed successfully!"], 200);
        } else {
            $favorite = Favorite::create([
                "client_id" => $clintId,
                "consultant_id" => $consultantId
            ]);
            return response()->json(["message" => "consultant added successfully!"], 200);
        }
        //

    }

    public function getFavorite($clintId)
    {

        $consids = Favorite::where('client_id', $clintId)->pluck('consultant_id');
        $consultants = [];
        for ($i = 0; $i < count($consids); $i++) {
            $consultant = User::query()
                ->join('consultants', 'users.id', '=', 'consultants.user_id')
                ->where(function ($q) use ($consids, $i) {
                    $q->where('users.id', $consids[$i]);
                })
                ->get();

            array_push($consultants, $consultant[0]);
        }
        return response()->json(["data" => $consultants]);
    }

    public function getFavoriteId($clintId)
    {

        $consids = Favorite::where('client_id', $clintId)->pluck('consultant_id');
        $consultants = [];
        for ($i = 0; $i < count($consids); $i++) {
            $consultant = User::query()
                ->join('consultants', 'users.id', '=', 'consultants.user_id')
                ->where(function ($q) use ($consids, $i) {
                    $q->where('users.id', $consids[$i]);
                })
                ->pluck('users.id');

            array_push($consultants, $consultant[0]);
        }
        return $consultants;
    }
}
