<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Consultant;

use function PHPUnit\Framework\countOf;

class FavoriteController extends Controller
{
    //
 public function addFavorite(Request $request){
   $request->validate([
    "add"=>"required|boolean",
    "clientid"=>"required",
    "consultantid"=>"required"
   ]);

   $add=$request->add;
   $clintId=$request->clientid;
   $consultantId=$request->consultantid;

if($add==false){
    $user = Favorite::where(function ($q) use ($clintId, $consultantId) {
        $q->where('client_id', $clintId)
            ->where('consultant_id', $consultantId);
    })->delete();}
    
    else{
    $favorite=Favorite::create([
        "client_id" => $clintId,
    "consultant_id" => $consultantId]);}
   // 
         
} 

public function getFavorite($clintId){

   $consids=Favorite::where('client_id',$clintId)->pluck('consultant_id');
   $consultants=[];
    for($i=0; $i<count($consids);$i++){
    $consultant = User::query()
    ->join('consultants', 'users.id', '=', 'consultants.user_id')
    ->where(function($q) use ($consids,$i){
       $q -> where('users.id', $consids[$i]);})
          ->get();
         
        array_push($consultants,$consultant[0]);
          }
          return response()->json(["data"=>$consultants]);
}

public function getFavoriteId($clintId){

    $consids=Favorite::where('client_id',$clintId)->pluck('consultant_id');
    $consultants=[];
     for($i=0; $i<count($consids);$i++){
     $consultant = User::query()
     ->join('consultants', 'users.id', '=', 'consultants.user_id')
     ->where(function($q) use ($consids,$i){
        $q -> where('users.id', $consids[$i]);})
           ->pluck('users.id');
          
         array_push($consultants,$consultant[0]);
           }
           return $consultants;
 }

}