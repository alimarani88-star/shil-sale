<?php

namespace App\Http\Requests\Customer;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddProductToCartRequest extends FormRequest
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
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('carts')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.unique' => 'این محصول قبلاً در سبد شما وجود دارد.',
        ];
    }

}
