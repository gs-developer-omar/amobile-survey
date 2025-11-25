<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SurveyYandexFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'params.phone' => [
                'required',
                'string',
                'digits:11',
                'starts_with:7',
            ],
        ];
    }

    /**
     * Подготовка данных для валидации.
     */
    protected function prepareForValidation(): void
    {
        $this->preparePhone();
    }

    /**
     * Подготовка номера телефона для валидации.
     */
    private function preparePhone(): void
    {
        $phone = $this->input('params.phone');

        if (!$phone) {
            return;
        }

        // preg_replace('/[^\d]/', '', $phone) удалит все, кроме цифр.
        $cleanPhone = preg_replace('/[^\d]/', '', $phone);

        if (Str::startsWith($cleanPhone, '8') && strlen($cleanPhone) === 11) {
            $cleanPhone = '7' . substr($cleanPhone, 1);
        }

        if (Str::length($cleanPhone) === 7) {
            $cleanPhone = '7940' . $cleanPhone;
        }

        $this->merge([
            'params' => [
                'phone' => $cleanPhone,
            ],
        ]);
    }
}
