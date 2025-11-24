<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyYandexFormRequest;
use App\Processors\SurveyYandexFormProcessor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SurveyYandexFormController extends Controller
{
    /**
     * @throws Exception
     */
    public function handle(SurveyYandexFormRequest $request, SurveyYandexFormProcessor $processor): JsonResponse
    {
        $phone = $request->validated('params.phone');

        return $processor->process($phone);
    }
}
