<?php

namespace App\Http\Requests\Admin;

use AllowDynamicProperties;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

 class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $post = $this->route('id'); // دریافت پست از route parameter در صورت وجود
        $rules = [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'title')->ignore($post),
            ],
        ];
        if ($this->prd_categories == '[]') {
            $this->merge([
                'prd_categories' => ''
            ]);
        }
        if ($this->categories == '[]') {
            $this->merge([
                'categories' => ''
            ]);
        }
        $rules = array_merge($rules, [
            'summary' => 'required|string',
            'content' => 'required|string',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1028',
            'type' => 'required|string|in:product,category,guide',
            'categories' => 'required_if:type,category|string',
            'primary_category' => 'nullable|required_if:type,category|string',
            'prd_categories' => 'required_if:type,product|string',
            'reading_time' => 'nullable|integer',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'alt_main_image' => 'nullable|string|max:128',

            'fa_name' => 'nullable|array',
            'fa_name.*' => 'nullable|string|max:128',

            'en_name' => 'nullable|array',
            'en_name.*' => 'nullable|string|max:128|regex:/^[a-zA-Z0-9 _-]+$/',

            'field_type' => 'nullable|array',
            'field_type.*' => 'nullable|string|in:text,link,video,audio,file',

            'value' => 'nullable|array',
            ]);



        return $rules;
    }
}
