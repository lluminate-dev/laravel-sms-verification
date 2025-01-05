<?php

namespace Lluminate\SmsVerification\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhoneVerificationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $cookie = $request->session()->get('sms-verified');

        // if azure user, bypass
        if (exists($user->azure_id) && $user->azure_id) {
            return $next($request);
        }

        // user hasn't got a phone, create-phone
        if (! $user->phone) {
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('sms.create');
        }

        // if user has no sms-verified cookie
        if (! $cookie) {

            $user->sendSmsVerificationNotification();
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('sms.notice'); //, ['uri' => $request->getRequestUri()]);

        }

        return $next($request);
    }

    public function clearCookie(Request $request)
    {
        $request->session()->forget('sms-verified');
    }

    public function flushSession(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->flush();
        $request->session()->regenerateToken();
    }

    public function logoutUser()
    {
        auth()->logout();
    }
}