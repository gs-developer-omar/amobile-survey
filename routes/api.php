<?php

use App\Http\Controllers\SurveyYandexFormController;
use Illuminate\Support\Facades\Route;

Route::middleware(['checkYandexForm'])->group(function () {
    Route::post('/survey-yandex-form/handle', [SurveyYandexFormController::class, 'handle']);
});
