<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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

Route::get('/firmwares', [ApiController::class, 'firmware']);
Route::get('/download/{id}', [ApiController::class, 'download']);
Route::get('/download-document/{id}', [ApiController::class, 'downloadDocument']);
Route::get('/technical-documentation', [ApiController::class, 'technicalDocumentation']);
Route::get('/certificates', [ApiController::class, 'certificates']);
