<?php

use Illuminate\Http\Request;
use App\Gates;
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

Route::get("/level/{gate}", function (Gates $gate) {
    return $gate->water_level;
});

Route::get("/info/{gate}", function (Gates $gate) {
    return $gate;
});
