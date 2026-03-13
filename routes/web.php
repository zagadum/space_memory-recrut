<?php
use App\Http\Controllers\Student\IndexController as StudentIndexController;
use App\Http\Controllers\Api\StudentCabinetController;
use Illuminate\Support\Facades\Route;



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
    return redirect('/register');
});

Route::get('/register', function () {
    return view('registration.index');
});


Route::any('/payments/imoje/test', 'App\Http\Controllers\ImojeController@payTest')->name('imoje.pay-test');

Route::any('/payments/imoje/failure', 'App\Http\Controllers\ImojeController@payFailure')->name('imoje.failure');
Route::any('/payments/imoje/success', 'App\Http\Controllers\ImojeController@paySuccess')->name('imoje.success');
Route::any('/payments/imoje/webhook', 'App\Http\Controllers\ImojeController@webhook')->name('imoje.webhook');
Route::post('/payments/imoje/return', 'App\Http\Controllers\ImojeController@return')->name('imoje.return');




//-------------- Admin part end ----



/**
 * Routes File manager
 */
/*
 Route::group(array('middleware' => ['isadmin', 'active'],'prefix' =>'fileman' ,'before' => \Config::get('roxy-fileman-laravel::base_auth_filters')), function()
 {
 Route::get('/', ['as' => 'fileman_index', 'before' => [], function(){return View::make('roxy-fileman.index', ['assets_path' => '/css/fileman/']);}]);
 Route::any('/dirtree', '\App\Http\Controllers\Fileman\FilemanController@DirTreeAction');
 Route::any('/fileslist', '\App\Http\Controllers\Fileman\FilemanController@FilesListAction');
 Route::any('/createdir', '\App\Http\Controllers\Fileman\FilemanController@CreateDirAction');
 Route::any('/deletedir', '\App\Http\Controllers\Fileman\FilemanController@DeleteDirAction');
 Route::any('/movedir', '\App\Http\Controllers\Fileman\FilemanController@MoveDirAction');
 Route::any('/copydir', '\App\Http\Controllers\Fileman\FilemanController@CopyDirAction');
 Route::any('/renamedir', '\App\Http\Controllers\Fileman\FilemanController@RenameDirAction');
 Route::any('/upload', '\App\Http\Controllers\Fileman\FilemanController@UploadAction');
 Route::any('/download', '\App\Http\Controllers\Fileman\FilemanController@DownloadAction');
 Route::any('/downloaddir', '\App\Http\Controllers\Fileman\FilemanController@DownloadDirAction');
 Route::any('/deletefile', '\App\Http\Controllers\Fileman\FilemanController@DeleteFileAction');
 Route::any('/movefile', '\App\Http\Controllers\Fileman\FilemanController@MoveFileAction');
 Route::any('/copyfile', '\App\Http\Controllers\Fileman\FilemanController@CopyFileAction');
 Route::any('/renamefile', '\App\Http\Controllers\Fileman\FilemanController@RenameFileAction');
 Route::any('/thumb', '\App\Http\Controllers\Fileman\FilemanController@ThumbAction');
 });
 */

//---- Файловый менеджер ----

Route::get('/admin', function () {

//  return redirect()->route('student-dashboard');

});



Route::middleware(['web'])->group(static function () {

    Route::namespace ('App\Http\Controllers\Auth')->group(static function () {
            Route::get('/admin/login', 'LoginController@showLoginForm')->name('login');
            Route::get('/admin/login/alias', function () {
                    return redirect()->route('login');
                }
                )->name('brackets/admin-auth::admin/login');
                //???  Route::get('/admin/login', 'LoginController@showLoginForm')->name('brackets/admin-auth::admin/login');
        
                Route::post('/admin/login', 'LoginController@login')->middleware('throttle:5,1');
                ; //see app\Http\Traits\AdminAuth\AuthenticatesUsers.php
                Route::any('/admin/logout', 'LoginController@logout')->name('logoutAdmin');
                Route::any('/logout', 'LoginController@logout')->name('logoutAny');
                Route::get('/admin/setlocation/{type}/{stay}', 'LoginController@setLocation')->name('setlocation');


            // Route::get('/admin/password-reset', 'ForgotPasswordController@showLinkRequestForm')->name('brackets/admin-auth::admin/password/showForgotForm');
            // Route::post('/admin/password-reset/send', 'ForgotPasswordController@sendResetLinkEmail');
            //  Route::get('/admin/password-reset/{token}', 'ResetPasswordController@showResetForm')->name('brackets/admin-auth::admin/password/showResetForm');
            //  Route::post('/admin/password-reset/reset', 'ResetPasswordController@reset');
        
            //  Route::get('/admin/activation/{token}', 'ActivationController@activate')->name('brackets/admin-auth::admin/activation');
            }
            );
        });



Route::middleware(['web'])->group(static function () {
    Route::namespace ('App\\Http\\Controllers\\Auth')->group(static function () {
            Route::get('/admin/activation', 'ActivationEmailController@showLinkRequestForm')->name('brackets/admin-auth::admin/activation');
            Route::post('/admin/activation/send', 'ActivationEmailController@sendActivationEmail');
        }
        );
    });
//--------------- AUTH END
//------------------------------------------------------------------------
//------- STUDENT -----------------------


Route::middleware(['is_auth'])->group(static function () {
    Route::group(['prefix' => 'student', 'as' => 'student.'], static function () {
            Route::any('/', function () {
                    return redirect('/test-parent-portal');
                }
                )->name('home.dashboard');
            }
            );
        });

Route::middleware(['is_student'])->group(static function () {
// Any specific student middleware routes can go here
});


Route::get('/verify', [StudentCabinetController::class , 'showVerifyPage']);
Route::get('/cabinet', [StudentCabinetController::class , 'showCabinetPage']);
//------ END STUDENT -----------------------------------------------------

/*
|--------------------------------------------------------------------------
| Father Portal (Кабинет Родителя)
|--------------------------------------------------------------------------
*/
Route::prefix('father')->middleware('is_auth')->group(function () {
    // Documents
    Route::get('/document', [\App\Http\Controllers\Father\DocumentController::class, 'index'])
        ->name('father.document');
    Route::get('/document-sign', [\App\Http\Controllers\Father\DocumentController::class, 'sign'])
        ->name('father.document-sign');

    // Payments
    Route::get('/payment', [\App\Http\Controllers\Father\PaymentController::class, 'index'])
        ->name('father.payment');
    Route::get('/payment-process', [\App\Http\Controllers\Father\PaymentController::class, 'process'])
        ->name('father.payment-process');
    Route::get('/payment-success', [\App\Http\Controllers\Father\PaymentController::class, 'success'])
        ->name('father.payment-success');
    Route::get('/payment-fail', [\App\Http\Controllers\Father\PaymentController::class, 'fail'])
        ->name('father.payment-fail');
    Route::get('/payment/download-invoice/{id}', [\App\Http\Controllers\Father\PaymentController::class, 'downloadInvoice'])
        ->name('father.download-invoice');

    // Learn
    Route::get('/learn', [\App\Http\Controllers\Father\LearnController::class, 'index'])
        ->name('father.learn');
});

Route::fallback(function () {
    abort(404);
});
/* Auto-generated admin routes */