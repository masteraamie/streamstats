<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StreamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [StreamController::class, 'index']);
Route::get('/auth/twitch', [LoginController::class, 'authTwitchAPI']);

Route::prefix('streams')->group(function () {
    Route::get('/getStreamsByGameName', [StreamController::class, 'getStreamsByGameName']);
    Route::get('/getTopGamesByViewerCount', [StreamController::class, 'getTopGamesByViewerCount']);
    Route::get('/getViewerCountMedian', [StreamController::class, 'getViewerCountMedian']);
    Route::get('/getTopStreamsByViewerCount', [StreamController::class, 'getTopStreamsByViewerCount']);
    Route::get('/getStreamsGroupedByStartTime', [StreamController::class, 'getStreamsGroupedByStartTime']);
    Route::get('/getTopStreamsFollowedByUser', [StreamController::class, 'getTopStreamsFollowedByUser']);
    Route::get('/getViewersRequiredToReachTop', [StreamController::class, 'getViewersRequiredToReachTop']);
    Route::get('/getTopStreamsUserSharedTags', [StreamController::class, 'getTopStreamsUserSharedTags']);
});
