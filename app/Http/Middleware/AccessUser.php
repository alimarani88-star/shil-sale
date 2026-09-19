<?php

namespace App\Http\Middleware;

use App\Services\AdminAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $access = app(AdminAccessService::class);
        $user = $access->currentUser();

        if (!$access->isStaff($user)) {
            return redirect()->to('/');
        }

        if ($access->allowsCurrentRoute($user)) {
            return $next($request);
        }

        $message = 'شما به این بخش دسترسی ندارید.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->route('A_home')->with('swal-error', $message);
    }
}
