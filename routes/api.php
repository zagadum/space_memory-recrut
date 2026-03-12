<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\FallbackController;


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

use App\Http\Controllers\Api\NewStudentsController;
use App\Http\Controllers\Api\LeadsController;
use App\Http\Controllers\Api\ExpelledStudentsController;

Route::post('/v1/register', [NewStudentsController::class, 'register']);

Route::prefix('v1')->group(function () {
    Route::get('/recruitment/new-students', [NewStudentsController::class, 'index']);
    Route::get('/recruitment/new-students/{id}', [NewStudentsController::class, 'show']);
    Route::get('/recruitment/new-students/{id}/history', [NewStudentsController::class, 'history']);
    Route::post('/recruitment/new-students', [NewStudentsController::class, 'store']);
    Route::post('/recruitment/new-students/{id}/archive', [NewStudentsController::class, 'archive']);
    
    Route::get('/recruitment/leads', [LeadsController::class, 'index']);
    Route::post('/recruitment/leads', [LeadsController::class, 'store']);
    Route::patch('/recruitment/leads/{id}', [LeadsController::class, 'update']);

    Route::get('/expelled-students', [ExpelledStudentsController::class, 'index']);
    Route::patch('/expelled-students/{id}', [ExpelledStudentsController::class, 'update']);
    Route::post('/expelled-students/{id}/archive', [ExpelledStudentsController::class, 'archive']);
});

Route::fallback(FallbackController::class);

