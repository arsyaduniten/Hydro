<?php

use Illuminate\Http\Request;
use App\Gates;
use App\GateRecord;
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

Route::get("/records/{gaterecord}", function (GateRecord $gaterecord) {
    return $gaterecord->records;
});

Route::get("/minmax/{token}", function ($token) {
	$id;
	if ($token == 'min'){
		// $id = Gates::select('id')->whereWaterLevel(Gates::min('water_level'))->get()[0]->id;
		$gate = Gates::orderBy('water_level', 'asc')->first(); // gets the whole row
		$id = $gate->id;
	} else if ($token == 'max') {
		// $id = Gates::select('id')->whereWaterLevel(Gates::max('water_level'))->get()[0]->id;
		$gate = Gates::orderBy('water_level', 'desc')->first(); // gets the whole row
		$id = $gate->id;
	}
    return $id;
});
