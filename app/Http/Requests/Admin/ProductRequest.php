<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $rules = [
            'product_name' => 'required|max:120|min:2',
            'price' => 'required|numeric',
            'price_unit' => 'required',
            'status' => 'required|numeric|in:0,1',
            'marketable' => 'required|numeric|in:0,1',
            'sales_start_date' => ['nullable', 'regex:~^14\d{2}/(0?[1-9]|1[0-2])/(0?[1-9]|[12][0-9]|3[01])$~'],
            'sales_end_date' => ['nullable', 'regex:~^14\d{2}/(0?[1-9]|1[0-2])/(0?[1-9]|[12][0-9]|3[01])$~'],
            'meta_key.*' => 'required',
            'meta_value.*' => 'required',
            'description' => 'required|max:1000|min:5',
        ];

//        $imageRules = 'required|image|mimes:png,jpg,jpeg,gif';

        if ($this->isMethod('post')) {
            $rules['product_group_id_in_app'] = 'required|numeric';
            $rules['product_name_id_in_app'] = 'required|numeric';
            $rules['image1'] = 'required|image|mimes:png,jpg,jpeg,gif';
            $rules['image2'] = 'image|mimes:png,jpg,jpeg,gif';
            $rules['image3'] = 'image|mimes:png,jpg,jpeg,gif';
            $rules['image4'] = 'image|mimes:png,jpg,jpeg,gif';
            $rules['image5'] = 'image|mimes:png,jpg,jpeg,gif';
        } else {
            $rules['image1'] = 'nullable|image|mimes:png,jpg,jpeg,gif';
            $rules['image2'] = 'nullable|image|mimes:png,jpg,jpeg,gif';
            $rules['image3'] = 'nullable|image|mimes:png,jpg,jpeg,gif';
            $rules['image4'] = 'nullable|image|mimes:png,jpg,jpeg,gif';
            $rules['image5'] = 'nullable|image|mimes:png,jpg,jpeg,gif';
        }

        return $rules;
    }

}
