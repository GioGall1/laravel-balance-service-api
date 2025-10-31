<?php

use Illuminate\Support\Facades\Route;
use App\Exceptions\UserNotFoundException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Эти маршруты получают префикс /api и middleware "api"
| (см. bootstrap/app.php → withRouting()).
*/

Route::get('/test-error', function () {
    throw new UserNotFoundException();
});