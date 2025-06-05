<?php

use App\Http\Controllers\Api\RateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/get-rates', [RateController::class, 'getRates']);
