<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerInfoRequest extends FormRequest
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
            'first_name'    => 'required|regex:/^[\p{L}\s]+$/u|min:2|max:30',
            'last_name'     => 'required|regex:/^[\p{L}\s]+$/u|min:2|max:30',
            'mobile'        => 'required|regex:/^09\d{9}$/|unique:exhibition_customers,mobile',
            'province'      => 'nullable|integer',
            'city'          => 'required|integer',
            'company_name'  => 'nullable|string|max:100',
            'description'   => 'nullable|string|max:500',
            'request_agency' => 'sometimes|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'وارد کردن نام الزامی است.',
            'first_name.regex'    => 'نام فقط باید شامل حروف باشد و نمی‌تواند عدد یا علامت داشته باشد.',
            'last_name.required'  => 'وارد کردن نام خانوادگی الزامی است.',
            'last_name.regex'     => 'نام خانوادگی فقط باید شامل حروف باشد و نمی‌تواند عدد یا علامت داشته باشد.',
            'mobile.required'     => 'وارد کردن شماره موبایل الزامی است.',
            'mobile.regex'        => 'شماره موبایل باید با 09 شروع شده و شامل 11 رقم باشد.',
            'mobile.unique'       => 'این شماره موبایل قبلاً ثبت شده است.',
            'province.integer'    => 'شناسه استان معتبر نیست.',
            'city.integer'        => 'شناسه شهر معتبر نیست.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'mobile' => convertPersianToEnglish($this->mobile),
        ]);
    }

}
