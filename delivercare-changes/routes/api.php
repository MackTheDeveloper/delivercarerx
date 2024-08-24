<?php

use App\Http\Controllers\API\v1\MainAPIController;
use App\Http\Controllers\API\v1\PatientController;
use App\Http\Controllers\API\v1\RefillsController;
use App\Http\Controllers\API\v1\RxsController;
use App\Http\Controllers\API\v1\DrugsController;
use App\Http\Controllers\API\v1\RefillShipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
	Route::post('/generate-token',[MainAPIController::class,'generateToken'])->name('generate-token');
    Route::get('/validate-token/{token}',[MainAPIController::class,'validateToken'])->name('validate-token');
    Route::get('/patient/list',[PatientController::class,'list'])->name('patient-list');
    Route::get('/rxs/list/{id}',[RxsController::class,'list'])->name('rxs-list');
    Route::get('/drugs/list',[DrugsController::class,'list'])->name('drugs-list');
    Route::get('/refill-shipment/list',[RefillShipmentController::class,'list'])->name('refill-shipment-list');
    Route::get('/refills/list', [RefillsController::class, 'list'])->name('refills-list');
    Route::get('/refills/details', [RefillsController::class, 'details'])->name('refills-details');
    Route::get('/refills/place-order', [RefillsController::class, 'placeRefillOrder'])->name('refills-place-order-api');
    Route::get('/patient/details', [PatientController::class, 'details'])->name('patient-details');
    Route::get('/rxs/list/{id}', [RxsController::class, 'list'])->name('rxs-list');

});
