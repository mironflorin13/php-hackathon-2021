<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;

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
Route::POST('/register',[AuthController::class,'register']);
Route::POST('/login',[AuthController::class,'login']);

Route::GET('/programme',[ProgrammeController::class,'index']);
Route::GET('/programme/create',[ProgrammeController::class,'create']);

Route::GET('/appointment/{cnp}',[AppointmentController::class,'showAll']);
Route::GET('/appointment/create',[AppointmentController::class,'create']);
Route::POST('/appointment',[AppointmentController::class,'store']);



Route::group(['middleware' =>['auth:sanctum']],function()
{
    Route::POST('/programme',[ProgrammeController::class,'store']);
    Route::DELETE('/programme/{id}',[ProgrammeController::class,'destroy']);

    Route::POST('/logout',[AuthController::class,'logout']);

    Route::GET('/appointment',[AppointmentController::class,'index']);
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

