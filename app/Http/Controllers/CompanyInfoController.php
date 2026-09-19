<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Models\CompanyInfo;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;

class CompanyInfoController extends Controller
{
    public function about()
    {
        $company_info = CompanyInfo::pluck('value',  'title');
        return view("PublicPages.about_company", compact('company_info'));
    }

    public function contact()
    {
        $company_info = CompanyInfo::pluck('value',  'title');

        return view("PublicPages.contact_company", compact('company_info'));
    }

    public function send_contact(ContactMessageRequest $request)
    {
        if (filled($request->input('website'))) {
            return redirect()
                ->route('contact')
                ->with('success', 'پیام شما با موفقیت ارسال شد.');
        }

        $validated = $request->safe()->only(['name', 'mobile', 'subject', 'message']);

        $recentDuplicate = ContactMessage::query()
            ->where('mobile', $validated['mobile'])
            ->where('ip', $request->ip())
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($recentDuplicate) {
            return redirect()
                ->route('contact')
                ->with('error', 'پیام شما اخیراً ارسال شده است. لطفاً کمی بعد دوباره تلاش کنید.');
        }

        ContactMessage::query()->create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('contact')
            ->with('success', 'پیام شما با موفقیت ارسال شد.');
    }

    public function order_guide()
    {
        return view('PublicPages.order_guide');
    }
    public function frequently_asked_questions()
    {
        $questions = CompanyInfo::where('title', 'question')->get();

        $questions_answers = [];

        foreach ($questions as $question) {
            $answer = CompanyInfo::where('parent_id', $question->id)
                ->where('title', 'answer')
                ->first();

            $questions_answers[$question->value] = $answer ? $answer->value : null;
        }


        return view('PublicPages.frequently_asked_questions', compact('questions_answers'));
    }

    public function privacy()
    {
        return view('PublicPages.privacy');
    }
    public function rules_regulations()
    {
        return view('PublicPages.rules_regulations');
    }

    public function return_order()
    {
        return view('PublicPages.return_order');
    }
}
