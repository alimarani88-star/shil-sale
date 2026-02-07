<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequests extends FormRequest
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
            'product_id' => 'required|numeric',
            'percentage_discount' => 'required|numeric',
            'status' => 'required',
            'percentage_start_date' => ['required', 'regex:~^14\d{2}/(0?[1-9]|1[0-2])/(0?[1-9]|[12][0-9]|3[01])$~'],
            'percentage_end_date' => ['required', 'regex:~^14\d{2}/(0?[1-9]|1[0-2])/(0?[1-9]|[12][0-9]|3[01])$~'],
        ];

    }

}
