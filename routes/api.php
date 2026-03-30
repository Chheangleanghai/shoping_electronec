<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\GoogleAuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| JWT Authentication & Admin Protected Routes
|--------------------------------------------------------------------------
*/

// ------------------ Public Routes ------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('/auth/google', [GoogleAuthController::class, 'googleLogin']);


// Public product routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Public order creation (checkout)
Route::post('/orders', [OrderController::class, 'store']); // guests can create order

    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

// ------------------ Authenticated Routes ------------------
Route::middleware('auth:api')->group(function () {
    // Basic user info
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //  User submits payment info after checkout (normal user)
    Route::post('/users/paid', [UserController::class, 'paid']);
    // ------------------ Admin-only Routes ------------------
    Route::middleware('admin')->group(function () {

        // Optional: Admin test route
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Welcome, Admin!']);
        });

        //  User management
        Route::get('/users', [UserController::class, 'index']);
        Route::patch('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        //  Product management

        //  Orders management for admin
        Route::get('/orders/paid', [OrderController::class, 'paidOrders']); // get only paid orders
        Route::get('/orders', [OrderController::class, 'index']); // optional: all orders
        Route::patch('/orders/{id}/paid', [OrderController::class, 'markPaid']); // mark paid
        Route::post('/users/paid', [UserController::class, 'paid']);


        //  Paid users list for admin dashboard
        Route::get('/paid-users', [UserController::class, 'getPaidUsers']);
    });
});
