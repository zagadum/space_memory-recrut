<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\FallbackController;
use App\Http\Controllers\Api\NewStudentsController;
use App\Http\Controllers\Api\LeadsController;
use App\Http\Controllers\Api\ExpelledStudentsController;
use App\Http\Controllers\Api\StudentCabinetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public routes: registration and email verification (no token required).
| Protected routes: all recruitment management endpoints (JWT required).
|
*/

// ─── Public ───────────────────────────────────────────────────────────────────
// Ученик регистрируется и верифицирует email без токена

Route::middleware('api.locale')->group(function () {
    Route::post('/v1/register', [NewStudentsController::class, 'register']);
});

// Route::prefix('v1/recruitment')->middleware('throttle:5,15')->group(function () {
//     Route::post('/verify-code', [StudentCabinetController::class, 'verifyCode']);
//     Route::post('/resend-code', [StudentCabinetController::class, 'resendCode']);
// });

// ─── Protected (JWT) ──────────────────────────────────────────────────────────
// Все эндпоинты управления учениками доступны только с валидным JWT

Route::middleware('verify.jwt')->prefix('v1')->group(function () {

    // Новые ученики
    Route::get('/recruitment/new-students',             [NewStudentsController::class, 'index']);
    Route::get('/recruitment/new-students/{id}',        [NewStudentsController::class, 'show']);
    Route::get('/recruitment/new-students/{id}/history',[NewStudentsController::class, 'history']);
    Route::post('/recruitment/new-students',            [NewStudentsController::class, 'store']);
    Route::patch('/recruitment/new-students/{id}',      [NewStudentsController::class, 'update']);
    Route::post('/recruitment/new-students/{id}/archive',[NewStudentsController::class, 'archive']);

    // Лиды
    Route::get('/recruitment/leads',         [LeadsController::class, 'index']);
    Route::post('/recruitment/leads',        [LeadsController::class, 'store']);
    Route::patch('/recruitment/leads/{id}',  [LeadsController::class, 'update']);

    // Выписанные ученики
    Route::get('/expelled-students',                    [ExpelledStudentsController::class, 'index']);
    Route::patch('/expelled-students/{id}',             [ExpelledStudentsController::class, 'update']);
    Route::post('/expelled-students/{id}/archive',      [ExpelledStudentsController::class, 'archive']);

    // Платежи (GLS)
    Route::get('/payments/student/{id}',            [\App\Http\Controllers\Api\Payments\PaymentController::class, 'getStudentPayments']);
    Route::get('/payments/projects/{id}/transactions', [\App\Http\Controllers\Api\Payments\PaymentController::class, 'getStudentProjectTransactions']);
    Route::get('/payments/documents/{id}/pdf',      [\App\Http\Controllers\Api\Payments\DocumentController::class, 'downloadPdf']);

    // Массовые рассылки и импорт
    Route::get('/recruiting/campaigns',                [\App\Http\Controllers\Api\RecruitingCampaignController::class, 'index']);
    Route::post('/recruiting/campaigns',               [\App\Http\Controllers\Api\RecruitingCampaignController::class, 'store']);
    Route::post('/recruiting/campaigns/{id}/import',    [\App\Http\Controllers\Api\RecruitingCampaignController::class, 'import']);
    Route::post('/recruiting/campaigns/{id}/dry-run',   [\App\Http\Controllers\Api\RecruitingCampaignController::class, 'dryRun']);
    Route::post('/recruiting/campaigns/{id}/start',     [\App\Http\Controllers\Api\RecruitingCampaignController::class, 'start']);
    Route::get('/recruiting/campaigns/{id}/stats',      [\App\Http\Controllers\Api\RecruitingCampaignController::class, 'stats']);
});

Route::fallback(FallbackController::class);
