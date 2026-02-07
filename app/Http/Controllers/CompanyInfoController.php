<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;

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
