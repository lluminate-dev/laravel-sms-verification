<?php

namespace Lluminate\SmsVerification\Clients;

use Twilio\Rest\Client;

class TwilioClient
{
    protected Client $client;
    protected string $serviceSid;

    public function __construct(
        string $accountSid,
        string $authToken,
        string $serviceSid
    ) {
        $this->client = new Client($accountSid, $authToken);
        $this->serviceSid = $serviceSid;
    }

    /**
     * Send a verification code via SMS.
     */
    public function sendSmsVerification(string $to): void
    {
        $this->client->verify->v2
            ->services($this->serviceSid)
            ->verifications
            ->create($to, 'sms');
    }

    /**
     * Check a verification code.
     */
    public function checkVerificationCode(string $to, string $code): bool
    {
        $verificationCheck = $this->client->verify->v2
            ->services($this->serviceSid)
            ->verificationChecks
            ->create([
                'to' => $to,
                'code' => $code,
            ]);

        return $verificationCheck->status === 'approved';
    }
}