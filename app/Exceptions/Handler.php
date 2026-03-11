<?php

namespace App\Exceptions;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException as TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\ThrottleRequestsException;
use Whoops\Handler\PrettyPageHandler;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramNotifier;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
          \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,

    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'MAIL_USERNAME'
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->path() === 'api') {
                return response()->json([
                    'message' => 'Not Found',
                    'path' => '/'.$request->path(),
                    'url' => $request->fullUrl(),
                ], 404);
            }

            return null;
        });


        $this->reportable(function (Throwable $e) {
            if ($e instanceof QueryException) {
                Log::channel('sql')->error('SQL Error: ' . $e->getMessage(), [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                ]);
            }
        });
        if (config('app.debug') && app()->bound('whoops.handler')) {
            $whoopsHandler = app('whoops.handler');
            if ($whoopsHandler instanceof PrettyPageHandler) {
                // Убрать секции Server/Request Data, Environment Variables и др.
                $whoopsHandler->setEditor(null); // Убрать редактор
                $whoopsHandler->hideSuperfluousData(); // Убрать лишние данные
            }
        }

        $this->reportable(function (Throwable $e) {

        });
    }

//    public function render($request, Throwable $exception)
//    {
//
////        if ($exception instanceof \Illuminate\Database\QueryException) {
////            return response()->view('errors.sql', [], 500);
////        }
//
//        if ($exception instanceof MethodNotAllowedHttpException) {
//            // для HTML-запитів
//            if ($request->wantsHtml()) {
//                return response()->view('errors.405', [], 405);
//            }
//            // для JSON
//            return response()->json(['error' => 'Метод не дозволений'], 405);
//        }
//
//        if ($exception instanceof ThrottleRequestsException) {
//
//            return response()->view('errors.too_many_requests', [], 429);
//        }
//
//
////        if (config('app.debug') === false && $response->getStatusCode() === 500) {
////            $data = $response->getData(true);
////            unset($data['trace']); // Удаляем стек
////            $response->setData($data);
////        }
//        return parent::render($request, $exception);
//    }
}
