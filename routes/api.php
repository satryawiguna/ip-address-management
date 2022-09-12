<?php

use App\Presentation\Http\Controllers\Api\V1\Auth\AuthController;
use App\Presentation\Http\Controllers\Api\V1\Auth\UserController;
use App\Presentation\Http\Controllers\Api\V1\Manage\IpAddressController;
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
    Route::post('/login', [AuthController::class, "actionLogin"])->name('api.auth.login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [AuthController::class, "actionLogout"])->name('api.auth.logout');
    });
});

Route::group(['prefix' => 'manage'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/ip/', [IpAddressController::class, "actionAll"])->name('api.manage.ip.all');
        Route::get('/ip/{id}', [IpAddressController::class, "actionGet"])->name('api.manage.ip.get');
        Route::post('/ip/search', [IpAddressController::class, "actionSearch"])->name('api.manage.ip.search');

        Route::group(['prefix' => 'ip'], function () {
            Route::post('/search/page/{perPage}/{page}/{order?}/{sort?}', [IpAddressController::class, "actionSearchPage"])->name('api.manage.ip.search.page');
        });

        Route::post('/ip/', [IpAddressController::class, "actionStore"])->name('api.manage.ip.store');
        Route::put('/ip/', [IpAddressController::class, "actionStore"])->name('api.manage.ip.update');
        Route::delete('/ip/{id}', [IpAddressController::class, "actionDestroy"])->name('api.manage.ip.destroy');
    });
});




