<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCodeRequest extends FormRequest
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
            "verification_code"=> "required|min:4|max:4",
            "username"=> "required|min:11|max:11",
        ];
    }

    public function attributes(): array
    {
        return [
            'verification_code' => 'کد تأیید',
            'username' => 'شماره موبایل',
        ];
    }

    public function messages(): array
    {
        return [
            'verification_code.required' => 'وارد کردن کد تأیید الزامی است.',
            'verification_code.min' => 'کد تأیید باید ۴ رقم باشد.',
            'verification_code.max' => 'کد تأیید باید ۴ رقم باشد.',
            'username.required' => 'شماره موبایل الزامی است.',
            'username.min' => 'شماره موبایل باید ۱۱ رقم باشد.',
            'username.max' => 'شماره موبایل باید ۱۱ رقم باشد.',
        ];
    }
}
