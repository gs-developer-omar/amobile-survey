<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyFormRequest;
use Illuminate\Support\Facades\Log;

class SurveyFormController extends Controller
{
    public function process(SurveyFormRequest $request)
    {
        $phone = $request->validated('phone');

        Log::channel('survey')->info('phone', [$phone]);

        // Шаг1: Проверка существования номера телефона
        // Шаг2: Проверка, что у этого номера телефона тариф вайб
        // Шаг3: Проверка, что по этому номеру пользователь ранее не проходил опрос
        // Шаг4: Добавление 2ГБ
        // Шаг5: Отправить пользователю смс-уведомление о зачислении ГБ
    }
}
