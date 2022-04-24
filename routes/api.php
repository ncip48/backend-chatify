<?php

use App\Http\Controllers\AuthController;
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

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile']);
    Route::get('/users', [App\Http\Controllers\UserController::class, 'allUsers']);
    Route::patch('/update-profile', [App\Http\Controllers\UserController::class, 'updateProfile']);

    Route::get('/chat/{target_user_id}', [App\Http\Controllers\ChatController::class, 'getChats']);
    Route::post('/chat', [App\Http\Controllers\ChatController::class, 'sendChat']);
    Route::get('/contacts', [App\Http\Controllers\UserController::class, 'getContacts']);
    Route::post('/search-users', [App\Http\Controllers\UserController::class, 'searchUsers']);
    // API route for logout user
    Route::post('/auth/logout', [App\Http\Controllers\AuthController::class, 'logout']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', AuthController::class . '@register');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
});

Route::post('/upload-image', [App\Http\Controllers\UploadController::class, 'upload']);
Route::delete('/delete-image', [App\Http\Controllers\UploadController::class, 'delete']);

Route::post('/test-broadcast', [App\Http\Controllers\ChatController::class, 'testBroadcast']);
