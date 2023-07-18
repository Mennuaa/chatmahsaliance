<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PusherAuthController;
use Artisan as GlobalArtisan;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/{id}', [ProfileController::class, 'show']);
    
    Route::patch('profile/update', [ProfileController::class,'update']);
    
    Route::post('/find-user', [UserController::class, 'search'] );
    Route::post('/conversation/create' , [ConversationController::class, 'create'])->name('conversation.create');
    Route::post('conversation/send-message', [ConversationController::class, 'sendMessage']);
    Route::get('conversations', [ConversationController::class,'getConversations']);
    Route::get('conversation/{id}', [ConversationController::class, 'getConversation']);
    
    
});
Route::post('logout',[AuthController::class, 'logout']);
Route::post('/pusher/auth', [PusherAuthController::class, 'authenticate']);
