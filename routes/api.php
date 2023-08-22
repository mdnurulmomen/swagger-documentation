<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\PromotionController;
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
Route::name('main.')->group(function () {

    Route::prefix('v1/main')->group(function () {

        Route::get('/promotions', [PromotionController::class, 'getPromotionList'])->name('promotions.index');
        Route::get('/blog', [PostController::class, 'index'])->name('blogs.index');

    });

});


Route::name('admin.')->group(function () {

    Route::prefix('v1/admin')->group(function () {

        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::middleware(['auth.jwt', 'admin'])->group(function () {

            Route::post('/create', [UserController::class, 'store'])->name('store');

            Route::get('/user-listing', [UserController::class, 'getUserList'])->name('users.index');

            Route::put('/user-edit/{uuid}', [UserController::class, 'updateUser'])->name('users.update');

            Route::delete('/user-delete/{uuid}', [UserController::class, 'deleteUser'])->name('users.destroy');

            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        });

    });

});


Route::name('user.')->group(function () {

    Route::prefix('v1/user')->group(function () {

        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [AuthController::class, 'getResetToken'])->name('reset-token');
        Route::post('/reset-password-token', [AuthController::class, 'resetPassword'])->name('reset-password');

        Route::middleware(['auth.jwt'])->group(function () {

            Route::middleware(['admin'])->group(function () {

                Route::post('/create', [UserController::class, 'store'])->name('store');
                Route::delete('/', [AuthController::class, 'delete'])->name('destroy');

            });

            Route::get('/', [AuthController::class, 'me'])->name('show');

            Route::get('/orders', [UserController::class, 'getUserOrders'])->name('orders.index');
            Route::put('/edit', [UserController::class, 'update'])->name('update');

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


