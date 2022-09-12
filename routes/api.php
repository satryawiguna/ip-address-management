<?php

use App\Presentation\Http\Controllers\Api\V1\Auth\AuthController;
use App\Presentation\Http\Controllers\Api\V1\Auth\UserController;
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

Route::post('/register', [UserController::class, "actionRegister"])->name('api.register');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, "actionLogin"])->name('api.login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [AuthController::class, "actionLogout"])->name('api.logout');
    });
});



