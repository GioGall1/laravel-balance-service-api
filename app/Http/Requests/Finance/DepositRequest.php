<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидатор запроса на пополнение баланса пользователя.
 */
class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {   
        // Пока разрешаем всем — можно добавить авторизацию позже.
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'comment' => ['nullable', 'string', 'max:255'],
        ];
    }
}