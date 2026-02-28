<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Order;
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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

include __DIR__."/api/courier/v2.php";

//Caller App
Route::post('/caller/login', [App\Http\Controllers\CallerController::class, 'login']);
Route::get('/caller/phoneNumber/{userId}/{phone}', [App\Http\Controllers\CallerController::class, 'getphoneNumber']);



