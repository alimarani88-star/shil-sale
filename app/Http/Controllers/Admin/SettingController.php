<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\EditCompanyInfoRequest;
use App\Models\CompanyInfo;

use function PHPUnit\Framework\callback;

class SettingController extends Controller
{
    public function A_setting()
    {
    }
    public function A_edit_contact()
    {
        $company_info = CompanyInfo::pluck('value', key: 'title');
        return view("Admin.Setting.A_edit_contact", compact('company_info'));
    }
    public function A_edit_about()
    {
        $company_info = CompanyInfo::where('title', 'text_about')->first();
        return view("Admin.Setting.A_edit_about", compact('company_info'));
    }
 public function A_edit_frequently_asked_questions()
{
    $questions = CompanyInfo::where('title', 'question')->get();

    $questions_answers = [];

    foreach ($questions as $question) {
        $answer = CompanyInfo::where('parent_id', $question->id)
            ->where('title', 'answer')
            ->first();

        $questions_answers[] = [
            'id' => $question->id,
            'question' => $question->value,
            'answer' => $answer ? $answer->value : '',
        ];
    }

    return view("Admin.Setting.A_edit_frequently_asked_questions", compact('questions_answers'));
}


    public function A_s_edit_company_info(Request $request)
    {
        $data = [
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'email' => $request->email,
            'instagram' => $request->instagram,
            'linkedin' => $request->linkedin,
            'telephone' => $request->telephone,
            'mobile' => $request->mobile,
            'work_hours' => $request->work_hours,
        ];

        foreach ($data as $title => $value) {
            CompanyInfo::where('title', $title)->update(['value' => $value]);
        }

        return back()->with('success', 'اطلاعات شرکت با موفقیت ویرایش شد');
    }


    public function A_s_edit_about(Request $request)
    {
        $company_info = CompanyInfo::where('title', 'text_about')->first();

        if ($company_info) {
            $company_info->update([
                'value' => $request->about_text,
            ]);
        }

        return back()->with('success', 'اطلاعات متن درباره ما با موفقیت ویرایش شد');
    }


    public function A_s_edit_frequently_asked_questions(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $question = CompanyInfo::create([
            'title' => 'question',
            'value' => $request->input('question'),
            'parent_id' => null,
        ]);

        CompanyInfo::create([
            'title' => 'answer',
            'value' => $request->input('answer'),
            'parent_id' => $question->id,
        ]);

        return back()->with('success', 'سوال و جواب با موفقیت اضافه شد');
    }

    public function A_delete_frequently_asked_questions(Request $request)
    {
        CompanyInfo::where('parent_id', $request->id)->where('title', 'answer')->delete();

        CompanyInfo::where('id', $request->id)->where('title', 'question')->delete();

        return back()->with('toast-success', 'سوال و جواب با موفقیت حذف شد');
    }

    public function A_update_frequently_asked_questions(Request $request, $id)
    {

        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        CompanyInfo::where('id', $id)
            ->where('title', 'question')
            ->update(['value' => $request->input('question')]);

        CompanyInfo::where('parent_id', $id)
            ->where('title', 'answer')
            ->update(['value' => $request->input('answer')]);

        return back()->with('toast-success', 'سوال و جواب با موفقیت ویرایش شد');
    }

}
