<?php

use App\Enums\ERROR_TYPE;
use App\Exceptions\ApiExceptions;
use App\Http\Middleware\CheckSurveyYandexFormSecretKey;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkYandexForm' => CheckSurveyYandexFormSecretKey::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $exception = $e;
                $className = get_class($e);
                if ($e->getPrevious() !== null && get_class($e->getPrevious()) === ModelNotFoundException::class) {
                    $exception = $e->getPrevious();
                    $className = get_class($e->getPrevious());
                }
                $handlers = ApiExceptions::$handlers;

                if (array_key_exists($className, $handlers)) {
                    $method = $handlers[$className];
                    return ApiExceptions::$method($exception, $request);
                }

                if (config('app.debug') === true || config('app.env') === 'local') {
                    return response()->json([
                        'error' => [
                            'type' => basename(get_class($exception)),
                            'status' => intval($exception->getCode()),
                            'message' =>  $exception->getMessage()
                        ]
                    ]);
                }
                return response()->json([
                    'errors' => [
                        [
                            'type' => ERROR_TYPE::HTTP_INTERNAL_SERVER_ERROR,
                            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                            'message' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]
                        ]
                    ]
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();
