<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидатор запроса на перевод баланса пользователю.
 */
class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        // при необходимости можно добавить проверку ролей/прав
        return true;
    }

    public function rules(): array
    {
        return [
            'from_user_id' => ['required','integer','exists:users,id','different:to_user_id'],
            'to_user_id'   => ['required','integer','exists:users,id'],
            'amount'       => ['required','numeric','min:0.01'],
            'comment'      => ['nullable','string','max:255'],
        ];
    }
}