<?php

use App\Http\Controllers\SurveyFormController;
use Illuminate\Support\Facades\Route;

Route::post('/survey/form', [SurveyFormController::class, 'process']);
