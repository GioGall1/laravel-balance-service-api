<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\Finance\DepositController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Эти маршруты получают префикс /api и middleware "api"
| (см. bootstrap/app.php → withRouting()).
*/

Route::get('/balance/{userId}', [BalanceController::class, 'show']);
Route::post('/deposit', [DepositController::class, 'store']);