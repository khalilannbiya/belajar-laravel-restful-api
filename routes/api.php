<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('users')->name('users.')->group(function () {
    Route::post('/', [\App\Http\Controllers\UserController::class, 'register'])->name("register");
    Route::post('login', [\App\Http\Controllers\UserController::class, 'login'])->name("login");

    Route::middleware(['api.auth'])->group(function () {
        Route::get('current', [\App\Http\Controllers\UserController::class, 'get'])->name("get");
        Route::put('current', [\App\Http\Controllers\UserController::class, 'update'])->name("update");
    });
});
