<?php

namespace App\Processors;

use App\Gateways\BercutOracleProcedureGateway;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SurveyYandexFormProcessor
{
    public function __construct(public BercutOracleProcedureGateway $bercutGateway)
    {
    }

    /**
     * @throws Exception
     */
    public function process(string $phone): JsonResponse
    {
        // Шаг1: Проверка существования номера телефона
        // Шаг2: Проверка, что у этого номера телефона тариф вайб
        // Шаг3: Проверка, что по этому номеру пользователь ранее не проходил опрос
        // Шаг4: Добавление 2ГБ
        // Шаг5: Отправить пользователю смс-уведомление о зачислении ГБ

        $i_msisdn = $this->prepareMsisdn($phone);
        try {
            $procedureResult = $this->bercutGateway->gift2GB($i_msisdn);
        } catch (Exception $exception) {
            Log::channel('survey_yandex_form')->critical($phone, [
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }

        if ($procedureResult['resultCode'] !== 0) {
            Log::channel('survey_yandex_form')->error($phone, $procedureResult);
            return response()->json($procedureResult, 400);
        }

        Log::channel('survey_yandex_form')->info($phone, $procedureResult);
        return response()->json($procedureResult, 200);
    }

    private function prepareMsisdn(string $phone): string
    {
        return substr($phone, 4);
    }
}
