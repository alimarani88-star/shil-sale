<?php

namespace App\Http\Requests\Customer;

use App\Models\UserProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomerAddressRequest extends FormRequest
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
        $needsNationalCode = $this->needsNationalCode();

        return [
            'province'             => 'required|numeric',
            'city'                  => 'required|numeric',
            'address'               => 'required|string|min:10|max:500',
            'postal_code'           => 'required|digits:10',
            'no'                    => 'required|string|max:4',
            'unit'                  => 'nullable|string|max:3',
            'recipient_first_name'  => 'required|string|max:50',
            'recipient_last_name'   => 'required|string|max:50',
            'mobile'                => 'required|regex:/^09\d{9}$/',
            'national_code'         => $needsNationalCode
                ? 'required|digits:10'
                : 'nullable|digits:10',
        ];
    }

    public function messages(): array
    {
        return [
            'national_code.required' => 'لطفاً کد ملی را وارد کنید.',
            'national_code.digits'   => 'کد ملی باید ۱۰ رقم باشد.',
        ];
    }

    protected function prepareForValidation()
    {
        $data = [
            'mobile' => convertPersianToEnglish((string) $this->mobile),
            'postal_code' => convertPersianToEnglish((string) $this->postal_code),
        ];

        if ($this->filled('national_code') || $this->has('national_code')) {
            $data['national_code'] = convertPersianToEnglish((string) $this->national_code);
        }

        $this->merge($data);
    }

    private function needsNationalCode(): bool
    {
        $nationalCode = UserProfile::where('user_id', Auth::id())->value('national_code');

        return blank($nationalCode);
    }
}
