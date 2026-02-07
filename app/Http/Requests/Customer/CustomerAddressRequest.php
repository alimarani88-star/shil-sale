<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

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
//        dd($this->all());
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
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'mobile' => convertPersianToEnglish($this->mobile),
            'postal_code' => convertPersianToEnglish($this->postal_code),
        ]);
    }
}
