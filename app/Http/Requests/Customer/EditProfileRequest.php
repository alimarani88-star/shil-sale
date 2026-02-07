<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
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
            'first_name'         => 'required|regex:/^[\p{L}\s]+$/u|min:2|max:30',
            'last_name'          => 'required|regex:/^[\p{L}\s]+$/u|min:2|max:30',
            'national_code'      => 'nullable|digits:10',
            'card_number'        => 'nullable|digits:16',
            'email'              => 'nullable|email|max:100',
            'company_national_id'=> 'nullable|digits:11',
            'economic_number'    => 'nullable|digits:12',
            'type'               => 'nullable|string|max:20'
        ];
    }


    public function messages(): array
    {
        return [
            'first_name.required'  => 'لطفاً نام را وارد کنید.',
            'first_name.string'    => 'نام باید فقط شامل حروف باشد.',
            'first_name.min'       => 'نام باید حداقل ۲ کاراکتر باشد.',
            'first_name.max'       => 'نام نباید بیشتر از ۵۰ کاراکتر باشد.',

            'last_name.required'   => 'لطفاً نام خانوادگی را وارد کنید.',
            'last_name.string'     => 'نام خانوادگی باید فقط شامل حروف باشد.',
            'last_name.min'        => 'نام خانوادگی باید حداقل ۲ کاراکتر باشد.',
            'last_name.max'        => 'نام خانوادگی نباید بیشتر از ۵۰ کاراکتر باشد.',

            'national_code.digits'       => 'کد ملی باید ۱۰ رقم باشد.',
            'card_number.digits'         => 'شماره کارت باید ۱۶ رقم باشد.',
            'email.email'                => 'ایمیل وارد شده صحیح نیست.',
            'email.max'                  => 'ایمیل نباید بیشتر از ۱۰۰ کاراکتر باشد.',

            'company_national_id.digits' => 'شناسه ملی شرکت باید ۱۱ رقم باشد.',
            'economic_number.digits'     => 'کد اقتصادی باید ۱۲ رقم باشد.',
            'type.string'                => 'نوع باید رشته باشد.',
            'type.max'                   => 'نوع نباید بیشتر از ۲۰ کاراکتر باشد.',
        ];
    }
}
