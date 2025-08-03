<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubscriptionController;
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

// Public authentication endpoints
Route::post('/auth', [AuthController::class, 'authenticate']);
Route::post('/auth/verify', [AuthController::class, 'verify']);

// Protected API endpoints (require valid token)
Route::middleware('api')->group(function () {
    // Subscription management
    Route::get('/users/{user}/subscriptions', [SubscriptionController::class, 'getUserSubscriptions']);
    Route::get('/users/{user}/access/{plan}', [SubscriptionController::class, 'checkUserAccess']);
    Route::post('/subscriptions', [SubscriptionController::class, 'createSubscription']);
    
    // Application data
    Route::get('/plans', [SubscriptionController::class, 'getAvailablePlans']);
    Route::get('/stats', [SubscriptionController::class, 'getApplicationStats']);
});