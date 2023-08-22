<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\UserController;

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

Route::name('admin.')->group(function () {

    Route::prefix('v1/admin')->group(function () {

        Route::post('/login', [AuthController::class, 'login'])->name('login');

    });

});


Route::name('user.')->group(function () {

    Route::prefix('v1/user')->group(function () {

        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [AuthController::class, 'getResetToken'])->name('reset-token');
        Route::post('/reset-password-token', [AuthController::class, 'resetPassword'])->name('reset-password');

        Route::middleware(['auth.jwt'])->group(function () {

            Route::middleware(['admin'])->group(function () {

                Route::post('/create', [UserController::class, 'storeUser'])->name('store');
                Route::delete('/', [AuthController::class, 'delete'])->name('delete');

            });

            Route::get('/', [AuthController::class, 'me'])->name('show');

            Route::get('/orders', [UserController::class, 'getUserOrders'])->name('orders.index');
            Route::put('/edit', [UserController::class, 'updateUser'])->name('users.update');

            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        });

    });

});

Route::fallback(function(){

    return response()->json([
        'success' => false,
        'data' => [],
        'error' => 'No endpoint found',
        'errors' => [],
        "extra" => []
    ], 404);

});


