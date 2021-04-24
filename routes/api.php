<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\PdfController;

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

Route::post('v1/register', [UserController::class, 'register']);
Route::post('v1/login', [UserController::class, 'login']);
Route::post('v1/tweets', [TweetController::class, 'store']);
Route::post('v1/friendships/{id}', [UserController::class, 'follow']);
Route::get('v1/report/download', [PdfController::class, 'pdfDownload']);
