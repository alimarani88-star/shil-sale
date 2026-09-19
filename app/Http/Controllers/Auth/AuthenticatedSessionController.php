<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\Response as Illuminate_Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store11111(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function store(LoginRequest $request): Illuminate_Response|RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $target = $this->redirectAfterLogin($request);

        if ($request->header('X-Inertia')) {
            return Inertia::location($target);
        }
        return redirect()->to($target);
    }

    private function redirectAfterLogin(Request $request): string
    {
        $user = $request->user();
        if ($user instanceof User && $user->isStaff()) {
            return route('A_home');
        }

        $home = route('home');
        $intended = $request->session()->pull('url.intended', $home);
        $path = parse_url($intended, PHP_URL_PATH) ?: '/';

        if ($path === '/dashboard' || str_starts_with($path, '/dashboard/') || str_starts_with($path, '/A_')) {
            return $home;
        }

        return $intended;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
