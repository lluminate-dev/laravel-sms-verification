<?php

namespace Lluminate\SmsVerification\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Twilio\Exceptions\RestException;
use Lluminate\SmsVerification\Clients\TwilioClient;

class SmsVerificationController extends Controller
{
    public function add()
    {
        if (Auth::user()->phone) {
            return redirect()->route('sms.notice');
        }

        return view('sms-verification::create-phone');
    }

    public function create(Request $request, TwilioClient $twilio)
    {
        $user = Auth::user();
        $request->validate([
            'phone' => 'nullable',
            'number' => ['required', 'string', 'min:10', 'unique:users,phone'],
            'country' => 'nullable|string',
        ]);

        $user->phone = $request['number'];
        $user->save();
        $user->sendSmsVerification($twilio);

        session()->flash('notification', 'Verification sms sent.');
        return redirect()->route('sms.notice');
    }

    public function passcode(Request $request, TwilioClient $twilio)
    {
        $user = Auth::user();
        if (! $user->phone) {
            return redirect()->route('sms.create');
        }

        $user->sendSmsVerification($twilio);
        return view('sms-verification::sms-verification');
    }

    public function verify(Request $request, TwilioClient $twilio)
    {
        $request->validate([
            'valid_from' => 'required',
            'passcode' => 'string|max:6|min:6|regex:/^[0-9]+$/',
            'requestUri' => 'nullable|string',
        ]);

        $user = Auth::user();

        if (config('sms-verification.twilio.bypass')) {
            if (request('passcode') == config('sms-verification.twilio.passcode')) {
                Session::put('sms-verified', true);
                return redirect()->intended('/dashboard');
            } else {
                session()->flash('message', 'The code you entered was incorrect.');
                return back();
            }
        }

        $correct = $twilio->checkVerificationCode($user->phone, request('passcode'));

        if ($correct) {
            
            Session::put('sms-verified', true);
            Session::forget('passcode-requested');

            $user->sms_verification_expires_at = null;
            $user->save();

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
            
        } else {
            
            session()->flash('message', 'The code you entered was incorrect.');
            return back();

        }

    }

    public function new(Request $request, TwilioClient $twilio)
    {
        $user = Auth::user();
        $user->sendSmsVerification($twilio, true);
        session()->flash('message', 'New code sent.');
        return redirect()->route('sms.notice');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('sms-verified');
        return redirect()->back();
    }
}