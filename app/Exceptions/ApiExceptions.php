<?php

namespace App\Exceptions;

use App\ERROR_TYPE;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptions extends Exception
{
    public static array $handlers = [
        NotFoundHttpException::class => 'handleNotFoundHttpException',
        ModelNotFoundException::class => 'handleModelNotFoundException',
        ValidationException::class => 'handleValidationException',
        MethodNotAllowedHttpException::class => 'handleMethodNotAllowedHttpException',
    ];

    public static function handleMethodNotAllowedHttpException(MethodNotAllowedHttpException $e, Request $request): JsonResponse
    {
        return response()->json([
            'errors' => [
                [
                    'type' => ERROR_TYPE::HTTP_METHOD_NOT_ALLOWED,
                    'status' => Response::HTTP_METHOD_NOT_ALLOWED,
                    'message' => rtrim($e->getMessage(), '.'),
                ]
            ]
        ], Response::HTTP_NOT_FOUND);
    }

    public static function handleNotFoundHttpException(NotFoundHttpException $e, Request $request): JsonResponse
    {
        return response()->json([
            'errors' => [
                [
                    'type' => ERROR_TYPE::HTTP_NOT_FOUND,
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => rtrim($e->getMessage(), '.'),
                ]
            ]
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public static function handleModelNotFoundException(ModelNotFoundException $e, Request $request): JsonResponse
    {
        $model = class_basename($e->getModel());
        return response()->json([
            'errors' => [
                [
                    'type' => ERROR_TYPE::HTTP_MODEL_NOT_FOUND,
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => "Модель {$model} не найдена",
                ]
            ]
        ], Response::HTTP_NOT_FOUND);
    }

    public static function handleValidationException(ValidationException $e, Request $request): JsonResponse
    {
        $errors = [];
        foreach ($e->errors() as $attributeErrors)
            foreach ($attributeErrors as $message) {
                $errors[] = [
                    'type' => ERROR_TYPE::HTTP_UNPROCESSABLE_ENTITY,
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => rtrim($message, '.'),
                ];
            }

        return response()->json([
            'errors' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
