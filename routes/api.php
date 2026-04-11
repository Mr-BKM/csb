<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\Api\IssuingController;
use App\Http\Controllers\API\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('customer-api',CustomerController::class);
Route::apiResource('item-api',ItemController::class);
Route::apiResource('issuing-api',IssuingController::class);
Route::post('/login', [AuthController::class, 'login']);
