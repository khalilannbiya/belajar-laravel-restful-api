<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (\Illuminate\Http\Request $request) {
//     return $request->user();
// });

Route::prefix('users')->name('users.')->group(function () {
    Route::post('/', [\App\Http\Controllers\AuthController::class, 'register'])->name("register");
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login'])->name("login");

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('current', [\App\Http\Controllers\UserController::class, 'get'])->name("get");
        Route::put('current', [\App\Http\Controllers\UserController::class, 'update'])->name("update");
        Route::delete('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name("logout");
    });
});

Route::prefix('contacts')->name('contacts.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [\App\Http\Controllers\ContactController::class, 'create'])->name("create");
        Route::get('/{id}', [\App\Http\Controllers\ContactController::class, 'get'])->name("get")->where('id', '^[0-9]+$');
        Route::put('/{id}', [\App\Http\Controllers\ContactController::class, 'update'])->name("update")->where('id', '^[0-9]+$');
        Route::delete('/{id}', [\App\Http\Controllers\ContactController::class, 'delete'])->name("delete")->where('id', '^[0-9]+$');
        Route::get('/', [\App\Http\Controllers\ContactController::class, 'search'])->name("search");

        Route::post('/{id}/addresses', [\App\Http\Controllers\AddressController::class, 'create'])->name("create.addresses")->where('id', '^[0-9]+$');
        Route::get('/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\AddressController::class, 'get'])->name("get.addresses")->where(['idContact', 'idAddress'], '^[0-9]+$');
        Route::put('/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\AddressController::class, 'update'])->name("update.addresses")->where(['idContact', 'idAddress'], '^[0-9]+$');
        Route::delete('/{idContact}/addresses/{idAddress}', [\App\Http\Controllers\AddressController::class, 'delete'])->name("delete.addresses")->where(['idContact', 'idAddress'], '^[0-9]+$');
        Route::get('/{id}/addresses', [\App\Http\Controllers\AddressController::class, 'getList'])->name("get.addresses")->where('id', '^[0-9]+$');
    });
});
