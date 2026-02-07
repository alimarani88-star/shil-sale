<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Jobs\SendSMS;

use Illuminate\Http\Response as Illuminate_Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Illuminate_Response|RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|lowercase|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            //'email' => $request->email,
            'email' => 'test@test.com',
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));


        $mobile = $request->username;
        $code4 = rand(1000, 9999);
        session(['username' => $mobile]);
        session(['verification_code' => $code4]);
        session(['repetition_code' => 0]);
        $templateId = '305741';
        $parameters = [
            [
                "name" => "CODE",
                "value" => $code4
            ]
        ];
        //  Log::info('Sending SMS to ' . $mobile . ' with code ' . $code4);
        SendSMS::dispatch($mobile, $templateId, $parameters);

        if ($request->header('X-Inertia')) {
            return Inertia::location(route('mobile_number_verification'));
        }

        return to_route('mobile_number_verification');
    }
}
