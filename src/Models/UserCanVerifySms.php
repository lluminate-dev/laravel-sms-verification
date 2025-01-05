<?php

namespace Lluminate\SmsVerification\Models;

use Illuminate\Support\Facades\Session;
use Lluminate\SmsVerification\Clients\TwilioClient;

trait UserCanVerifySms
{
    public function sendSmsVerification(TwilioClient $twilio, bool $override = false): void
    {
        $sessionKey = 'passcode-requested';

        // Check bypass
        if (config('sms-verification.twilio.bypass')) {
            return;
        }

        if (
            $override ||
            (
                $this->sms_verification_expires_at &&
                $this->sms_verification_expires_at->isPast()
            ) ||
            ! $this->sms_verification_expires_at ||
            ! Session::get($sessionKey)
        ) {
            $twilio->sendSmsVerification($this->phone);

            $this->sms_verification_expires_at = now()->addMinutes(10);
            Session::put($sessionKey, now());
            $this->save();
        } else {
            if (
                Session::get($sessionKey) && 
                $this->sms_verification_expires_at && 
                $this->sms_verification_expires_at->isFuture()
            ) {
                Session::flash('message', 'A verification code has already been sent.');
                return;
            }
        }
    }
}