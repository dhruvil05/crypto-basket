<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Support\Facades\Toast;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!isKycApproved(Auth::id())) {
            Toast::error('KYC must be approved to use this feature.');
            return redirect()->route('platform.user.kyc');
        }
        return $next($request);
    }
}
