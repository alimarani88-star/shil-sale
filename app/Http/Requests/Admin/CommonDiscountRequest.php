<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CommonDiscountRequest extends FormRequest
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
            'discount_type' => 'required',
            'discount_id' => 'required',
            'main_group_id' => 'required_if:discount_type,maingroup|numeric',
            'group_id' => 'required_if:discount_type,group|numeric',
            'product_id' => 'required_if:discount_type,Product|numeric',
            'percentage_discount' => 'required|numeric',
            'description'=> 'string'
        ];
    }

    public function messages(): array
    {
        return [
            'main_group_id.required_if' => 'فیلد گروه اصلی کالا هنگامی که نوع تخفیف برابر با گروه اصلی کالا است، الزامی است.',
            'product_id.required_if' => 'فیلد نام کالا هنگامی که نوع تخفیف برابر با کالا است، الزامی است.',
            'group_id.required_if' => 'فیلد گروه کالا هنگامی که نوع تخفیف برابر با گروه است، الزامی است.',
            'discount_id.required' => 'فیلد نام تخفیف الزامی است'
        ];
    }
}
