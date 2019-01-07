<?php

use Guratr\CommandRunner\Http\Controllers\HistoryController;
use Guratr\CommandRunner\Http\Controllers\CommandsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/commands', CommandsController::class . '@index');
Route::post('/commands/{index}/run', CommandsController::class . '@run');

Route::get('/history', HistoryController::class . '@index');

