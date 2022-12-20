<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\SearchUser;
use App\Http\Controllers\AppointmentController;
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
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/register-consultant', [AuthController::class, 'registerAsConsultant']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/test', [AuthController::class, 'test']);
Route::get('/auth/test2', [AuthController::class, 'test2']);
//Consultant
Route::get('/consultant/consultants-classified-list', [ConsultantController::class, 'getClassifiedConsultant']);
Route::get('/consultant/consultants-list', [ConsultantController::class, 'getAllConsultants']);

//Appointments
Route::post('appointment/book', [AppointmentController::class, 'bookAppointment']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('consultant-details/{id}' , [ConsultantController::class , 'getConsultantDetails']);
Route::post('admin/add-cash',[AdminController::class,'addMoneyToWallet']);
Route::post('search',[ConsultantController::class,'Search']);
