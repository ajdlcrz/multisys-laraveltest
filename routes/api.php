<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::post('/order', [ProductsController::class,'order']);
});

Route::post('/register', [LoginController::class,'store']);
Route::post('/login', [LoginController::class,'login']);
