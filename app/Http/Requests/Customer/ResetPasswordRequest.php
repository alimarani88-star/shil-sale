<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'different:old_password'],
            'confirm_new_password' => ['required', 'same:new_password'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'old_password.required' => 'لطفاً رمز عبور قبلی خود را وارد کنید.',
            'old_password.min' => 'رمز عبور قبلی باید حداقل ۸ کاراکتر باشد.',
            'new_password.required' => 'لطفاً رمز عبور جدید را وارد کنید.',
            'new_password.min' => 'رمز عبور جدید باید حداقل ۸ کاراکتر باشد.',
            'new_password.different' => 'رمز جدید نباید با رمز قبلی یکسان باشد.',
            'confirm_new_password.required' => 'لطفاً تکرار رمز عبور جدید را وارد کنید.',
            'confirm_new_password.same' => 'رمز عبور جدید و تکرار آن مطابقت ندارند.',
        ];
    }
}
