<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mobileRules = ['required', 'regex:/^09\d{9}$/'];
        if ($this->loggedCustomerMobile() !== null) {
            $mobileRules = ['nullable', 'regex:/^09\d{9}$/'];
        }

        return [
            'name' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\p{L}\s\x{200C}]+$/u'],
            'mobile' => $mobileRules,
            'subject' => ['required', 'string', 'min:3', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'website' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'وارد کردن نام الزامی است.',
            'name.min' => 'نام باید حداقل ۲ حرف باشد.',
            'name.max' => 'نام نمی‌تواند بیشتر از ۸۰ حرف باشد.',
            'name.regex' => 'نام فقط باید شامل حروف باشد.',
            'mobile.required' => 'وارد کردن شماره موبایل الزامی است.',
            'mobile.regex' => 'شماره موبایل باید با 09 شروع شده و ۱۱ رقم باشد.',
            'subject.required' => 'وارد کردن موضوع الزامی است.',
            'subject.min' => 'موضوع باید حداقل ۳ حرف باشد.',
            'subject.max' => 'موضوع نمی‌تواند بیشتر از ۱۲۰ حرف باشد.',
            'message.required' => 'وارد کردن متن پیام الزامی است.',
            'message.min' => 'متن پیام باید حداقل ۱۰ حرف باشد.',
            'message.max' => 'متن پیام نمی‌تواند بیشتر از ۲۰۰۰ حرف باشد.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام',
            'mobile' => 'شماره موبایل',
            'subject' => 'موضوع',
            'message' => 'متن پیام',
        ];
    }

    protected function prepareForValidation(): void
    {
        $accountMobile = $this->loggedCustomerMobile();

        $this->merge([
            'name' => $this->cleanText($this->input('name'), 80),
            'mobile' => $accountMobile ?? $this->normalizeMobile((string) $this->input('mobile', '')),
            'subject' => $this->cleanText($this->input('subject'), 120),
            'message' => $this->cleanText($this->input('message'), 2000),
        ]);
    }

    private function loggedCustomerMobile(): ?string
    {
        $user = $this->user();
        if (!$user instanceof User) {
            return null;
        }

        return $user->contactMobile();
    }

    private function cleanText(mixed $value, int $max): string
    {
        $text = trim(strip_tags((string) $value));
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return mb_substr($text, 0, $max);
    }

    private function normalizeMobile(string $mobile): string
    {
        $mobile = convertPersianToEnglish($mobile);
        $mobile = strtr($mobile, [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
        $mobile = preg_replace('/[\s\-]/', '', $mobile) ?? $mobile;

        if (str_starts_with($mobile, '+98')) {
            $mobile = '0' . substr($mobile, 3);
        } elseif (str_starts_with($mobile, '0098')) {
            $mobile = '0' . substr($mobile, 4);
        } elseif (str_starts_with($mobile, '98') && strlen($mobile) === 12) {
            $mobile = '0' . substr($mobile, 2);
        }

        return $mobile;
    }
}
