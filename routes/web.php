<?php

use App\Http\Controllers\Api\StudentCabinetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


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

//----------------------- BEGIN TEST

//return redirect('/close-site');

//----------------------- END TEST
//--------- Старый рабочий код
Route::get('/', function () {
    return redirect('/register');
});
Route::get('/login', function () {
    return redirect('/father/login');
});

Route::get('/register', function () {
    $registerFormToken = Str::random(64);
    session(['register_form_token' => $registerFormToken]);

    return view('registration.index', [
        'registerFormToken' => $registerFormToken,
    ]);
});


Route::any('/payments/imoje/test', 'App\Http\Controllers\ImojeController@payTest')->name('imoje.pay-test');

Route::any('/payments/imoje/failure', 'App\Http\Controllers\ImojeController@payFailure')->name('imoje.failure');
Route::any('/payments/imoje/success', 'App\Http\Controllers\ImojeController@paySuccess')->name('imoje.success');
Route::any('/payments/imoje/webhook', 'App\Http\Controllers\ImojeController@webhook')->name('imoje.webhook');
Route::post('/payments/imoje/return', 'App\Http\Controllers\ImojeController@return')->name('imoje.return');

//-------------- Admin part end ----




Route::middleware(['web'])->group(static function () {

    Route::namespace ('App\Http\Controllers\Auth')->group(static function () {
            //Route::get('/admin/login', 'LoginController@showLoginForm')->name('login');
            //Route::get('/admin/login/alias', function () {
            //        return redirect()->route('login');
            //    }
             //   )->name('brackets/admin-auth::admin/login');
                //???  Route::get('/admin/login', 'LoginController@showLoginForm')->name('brackets/admin-auth::admin/login');
        
             //   Route::post('/admin/login', 'LoginController@login')->middleware('throttle:5,1');
                ; //see app\Http\Traits\AdminAuth\AuthenticatesUsers.php
              //  Route::any('/admin/logout', 'LoginController@logout')->name('logoutAdmin');
                Route::any('/logout', 'LoginController@logout')->name('logoutAny');
            }
            );
        });

//--------------- AUTH END
//------------------------------------------------------------------------
//------- STUDENT -----------------------

//????????
Route::middleware(['is_auth'])->group(static function () {
    Route::group(['prefix' => 'student', 'as' => 'student.'], static function () {
            Route::any('/', function () {
                    return redirect('/test-parent-portal');
                }
                )->name('home.dashboard');
            }
            );
        });



Route::get('/verify', [StudentCabinetController::class, 'showVerifyPage'])->name('verification.show');
Route::post('/recruitment/verify-code', [StudentCabinetController::class, 'verifyCode'])
    ->middleware(['api.locale', 'verify.form:verify_form_token,/verify'])
    ->name('verification.verify');
Route::post('/recruitment/resend-code', [StudentCabinetController::class, 'resendCode'])
    ->middleware('verify.form:verify_form_token,/verify')
    ->name('verification.resend');
Route::get('/cabinet', [StudentCabinetController::class , 'showCabinetPage']);
// Recruiting
Route::get('/register/invite/{token}', [\App\Http\Controllers\Father\Invite\RecruitingInviteController::class , 'accept'])->name('recruiting.invite');
Route::get('/register/complete/{token}', [\App\Http\Controllers\Father\Invite\RegistrationCompletionController::class , 'index'])->name('registration.complete');
Route::post('/register/complete/{token}', [\App\Http\Controllers\Father\Invite\RegistrationCompletionController::class , 'store'])->name('registration.complete.store');

/*
 |--------------------------------------------------------------------------
 | Father Portal (Кабинет Родителя)
 |--------------------------------------------------------------------------
 */
Route::get('/father/login', [\App\Http\Controllers\Father\AuthController::class , 'showLogin'])
    ->name('father.login');
Route::post('/father/login', [\App\Http\Controllers\Father\AuthController::class , 'login'])
    ->name('father.login.submit');
Route::get('/father/logout', [\App\Http\Controllers\Father\AuthController::class , 'logout'])
    ->name('father.logout');

Route::prefix('father')
    ->middleware('is_father')
    ->group(function () {
        Route::get('/parent-portal', [\App\Http\Controllers\Father\Cabinet\FatherPortalController::class, 'index'])
            ->name('father.portal');

        Route::get('/documents', [\App\Http\Controllers\Father\Cabinet\FatherDocumentController::class, 'index'])
            ->name('father.documents');
        Route::get('/document', fn() => redirect()->route('father.documents'));
        Route::get('/document-view', fn() => redirect()->route('father.documents'));
        Route::get('/document-view/{document}', [\App\Http\Controllers\Father\Cabinet\FatherDocumentController::class, 'show'])
            ->name('father.document.view');
        Route::post('/documents/sign', [\App\Http\Controllers\Father\Cabinet\FatherDocumentController::class, 'sign'])
            ->name('father.documents.sign');

        Route::get('/payment', [\App\Http\Controllers\Father\Cabinet\FatherPaymentController::class, 'index'])
            ->name('father.payment');
        Route::post('/payment/create', [\App\Http\Controllers\Father\Cabinet\FatherPaymentController::class, 'create'])
            ->name('father.payment.create');
        Route::get('/payment-success', [\App\Http\Controllers\Father\Cabinet\FatherPaymentController::class, 'success'])
            ->name('father.payment.success');

        // Legacy / Common
        Route::get('/payment/download-invoice/{id}', [\App\Http\Controllers\Father\Cabinet\PaymentController::class, 'downloadInvoice'])
            ->name('father.download-invoice');
        Route::get('/learn', [\App\Http\Controllers\Father\LearnController::class, 'index'])
            ->name('father.learn');
    });

Route::get('/legal/terms', fn() => view('legal.terms'))->name('legal.terms');
Route::get('/legal/privacy', fn() => view('legal.privacy'))->name('legal.privacy');
Route::get('/legal/photo', fn() => view('legal.photo_consent'))->name('legal.photo');

Route::fallback(function () {
    abort(404);
});
