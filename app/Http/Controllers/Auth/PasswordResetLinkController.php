<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendSMS;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create_main(Request $request): Response
    {
        return Inertia::render('auth/forgot-password', [
            'status' => $request->session()->get('status'),
        ]);
    }
    public function create(Request $request): Response
    {
        return Inertia::render('auth/forgot-password', [
            'status' => $request->session()->get('status'),
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store_main(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09\d{9}$/'
        ]);

        $user = User::where('username', $request->mobile)->first();
        if (!$user) {
            return redirect()->back()->with(['error' => 'کاربری با این شماره موبایل یافت نشد.']);
        }

        $mobile = $request->mobile;
        $code4 = rand(1000, 9999);
        session(['forgot_password_mobile' => $mobile]);
        session(['forgot_password_code' => $code4]);
        session(['repetition_forgot_password_code' => 0]);
        $templateId = '305741';
        $parameters = [
            [
                "name" => "CODE",
                "value" => $code4
            ]
        ];
        SendSMS::dispatch($mobile, $templateId, $parameters);

        if ($request->header('X-Inertia')) {
            return Inertia::location(route('forgot_password_verification'));
        }

        return to_route('forgot_password_verification');

    }
}
