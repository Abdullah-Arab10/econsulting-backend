<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultantController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Auth
Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/register-consultant',[AuthController::class,'registerAsConsultant']);
Route::post('/auth/login',[AuthController::class,'login']);
Route::post('/auth/test',[AuthController::class,'test']);
//Consultant
Route::get('/consultant/consultants-list',[ConsultantController::class,'getAllConsultant']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('consultant-details/{id}' , [ConsultantController::class , 'getConsultantDetails']);
