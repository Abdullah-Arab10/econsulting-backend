<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\SearchUser;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FavoriteController;

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
Route::get('/consultant/consultants-list', [ConsultantController::class, 'getAllConsultants']);
Route::get('consultant/consultant-details/{id}', [ConsultantController::class, 'getConsultantDetails']);
Route::post('consultant/search', [ConsultantController::class, 'Search']);


//Appointments
Route::post('appointment/book', [AppointmentController::class, 'bookAppointment']);
Route::get('appointment/get-appointments/{id}',[AppointmentController::class,'getAppointments']);

//admin
Route::post('admin/add-cash', [AdminController::class, 'addMoneyToWallet']);

//
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//rating
Route::post('rate/{id}/{id1}',[ConsultantController::class,'rating']);


//favorite
Route::get('getFavorite/{id}',[FavoriteController::class,'getFavorite']);
Route::post('favorite',[FavoriteController::class, 'addFavorite']);
 