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

Route::GET('/programme',[ProgrammeController::class,'index']); //ruta ce imi va returna toate programele disponibile
Route::GET('/programme/create',[ProgrammeController::class,'create']); //ruta ce imi va crea o noua programare

Route::GET('/appointment/{cnp}',[AppointmentController::class,'showAll']); //ruta ce va afisa toate programmele la care este inscris utilizatorl in functie de cnp
Route::GET('/appointment/create',[AppointmentController::class,'create']);  //ruta ce imi va afisa toate programmele disponibile (returneaza acelasi lucru cu functia GET('/programme') de mai sus dar am decis sa o las pentru o mai buna implementare
Route::POST('/appointment',[AppointmentController::class,'store']); //ruta ce imi va returna o noua programare
Route::DELETE('/appointment/{id}',[AppointmentController::class,'destroy']); //ruta ce imi va sterge o rezervare in functie de id 

//rute acesibile doar pentru utilizatori autentificati
Route::group(['middleware' =>['auth:sanctum']],function()
{
    Route::GET('/appointment',[AppointmentController::class,'index']); //ruta ce imi va returna toate rezervarile facute 

    Route::POST('/programme',[ProgrammeController::class,'store']); //ruta ce imi va crea un nou programme
    Route::DELETE('/programme/{id}',[ProgrammeController::class,'destroy']); //ruta ce imi va sterge un program in functie de id 
    
    Route::POST('/logout',[AuthController::class,'logout']);
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

