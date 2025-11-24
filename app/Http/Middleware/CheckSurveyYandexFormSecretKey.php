<?php

namespace App\Http\Middleware;

use App\Enums\ERROR_TYPE;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSurveyYandexFormSecretKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secretKey = $request->header('X-SECRET-KEY');

        if (empty($secretKey)) {
            return response()->json([
                'errors' => [
                    [
                        'type' => ERROR_TYPE::HTTP_UNAUTHORIZED,
                        'status' => Response::HTTP_UNAUTHORIZED,
                        'message' => "Отсутсвует параметр безопасности",
                    ]
                ]
            ]);
        }

        $expectedSecretKey = config('survey_yandex_form.secret_key');

        if (empty($expectedSecretKey)) {
            return response()->json([
                'errors' => [
                    [
                        'type' => ERROR_TYPE::HTTP_INTERNAL_SERVER_ERROR,
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => "Внутрення ошибка сервера. Секретный ключ не указан",
                    ]
                ]
            ]);
        }

        if ($expectedSecretKey !== $secretKey) {
            return response()->json([
                'errors' => [
                    [
                        'type' => ERROR_TYPE::HTTP_UNAUTHORIZED,
                        'status' => Response::HTTP_UNAUTHORIZED,
                        'message' => "Неверный параметр безопасности",
                    ]
                ]
            ]);
        }

        return $next($request);
    }
}
