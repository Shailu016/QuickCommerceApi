<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/products', [ProductController::class, 'index']);
Route::post('/product', [ProductController::class, 'store']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);
Route::get('/products/list/{category}', [ProductController::class, 'productByCategory']);
Route::post('/products/update/{product}', [ProductController::class, 'update']);


Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::post('/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});


Route::prefix('cart')->group(function () {
    Route::get('/products', [CartProductController::class, 'index']);
    Route::post('/{product}/addToCart', [CartProductController::class, 'addToCart']);
    Route::delete('/{product}', [CartProductController::class, 'destroy']);
});

Route::prefix('order')->group(function () {
    Route::post('/checkout', [OrderController::class, 'orderPlace']);
    Route::post('/{order}/processing', [OrderController::class, 'updateStatusToProcesseing']);
    Route::post('/{order}/cancelled', [OrderController::class, 'updateStatusToCancelled']);
    Route::post('/{order}/completed', [OrderController::class, 'updateStatusToCompleted']);
});


Route::prefix('user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/reset-password', [AuthController::class, 'reset']);
    Route::post('/forgot-passworntd', [AuthController::class, 'forgetPassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post("/update", [AuthController::class, "userProfile"]);
});
