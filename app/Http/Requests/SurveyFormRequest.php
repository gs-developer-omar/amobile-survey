<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SurveyFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка данных для валидации (Очистка номера).
     */
    protected function prepareForValidation(): void
    {
        // 1. Извлекаем исходный номер из входных данных
        $phone = $this->input('params.phone');

        if ($phone) {
            // 2. Удаляем все символы, кроме цифр, а также знак '+'
            // preg_replace('/[^\d]/', '', $phone) удалит все, кроме цифр.
            $cleanPhone = preg_replace('/[^\d]/', '', $phone);

            // 3. Нормализация: если номер 11-значный и начинается с '8',
            // заменяем '8' на '7' (8926... -> 7926...)
            if (Str::startsWith($cleanPhone, '8') && strlen($cleanPhone) === 11) {
                $cleanPhone = '7' . substr($cleanPhone, 1);
            }

            // 4. Записываем очищенный номер обратно в запрос
            // Важно: мы меняем значение поля, которое будем валидировать.
            $this->merge([
                'params' => [
                    'phone' => $cleanPhone,
                ],
            ]);
        }
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
                // Проверяет, что номер состоит ровно из 11 цифр
                'digits:11',
                // Проверяет, что номер начинается именно с '7' (после очистки)
                'starts_with:7',
            ],
        ];
    }
}
