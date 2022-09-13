<?php

use App\Presentation\Http\Controllers\Api\V1\Auth\AuthController;
use App\Presentation\Http\Controllers\Api\V1\Auth\UserController;
use App\Presentation\Http\Controllers\Api\V1\Manage\IpAddressController;
use App\Presentation\Http\Controllers\Api\V1\Manage\LabelController;
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
        Route::put('/ip/{id}', [IpAddressController::class, "actionUpdate"])->name('api.manage.ip.update');
        Route::delete('/ip/{id}', [IpAddressController::class, "actionDestroy"])->name('api.manage.ip.destroy');


        Route::get('/label/', [LabelController::class, "actionAll"])->name('api.manage.label.all');
        Route::get('/label/{id}', [LabelController::class, "actionGet"])->name('api.manage.label.get');
        Route::post('/label/search', [LabelController::class, "actionSearch"])->name('api.manage.label.search');

        Route::group(['prefix' => 'label'], function () {
            Route::post('/search/page/{perPage}/{page}/{order?}/{sort?}', [LabelController::class, "actionSearchPage"])->name('api.manage.label.search.page');
        });

        Route::post('/label/', [LabelController::class, "actionStore"])->name('api.manage.label.store');
        Route::put('/label/{id}', [LabelController::class, "actionUpdate"])->name('api.manage.label.update');
        Route::delete('/label/{id}', [LabelController::class, "actionDestroy"])->name('api.manage.label.destroy');
    });
});




