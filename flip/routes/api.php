<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

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

Route::post('create_user', [UserController::class, 'create_user']);
Route::get('user', [UserController::class, 'user']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('balance_read', [UserController::class, 'balance_read']);
    Route::post('balance_topup', [UserController::class, 'balance_topup']);
    Route::post('transfer', [UserController::class, 'transfer']);
    Route::get('top_transactions_per_user', [TransactionController::class, 'top_transactions_per_user']);
    Route::get('top_users', [TransactionController::class, 'top_users']);
});