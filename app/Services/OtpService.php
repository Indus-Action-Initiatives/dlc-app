<?php

namespace App\Services;

use App\Contracts\SMSProvider;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(
        private readonly SMSProvider $smsProvider
    ) {
    }

    /**
     * Send OTP to the given mobile number via configured SMS provider.
     * In local/testing environment, logs only and returns true.
     */
    public function send(string $mobile, string $otp): bool
    {
        if (app()->environment(['local', 'testing'])) {
            Log::info("OTP (test mode) for {$mobile}: {$otp}");
            return true;
        }

        $mobile = $this->normalizeMobile($mobile);
        return $this->smsProvider->send($mobile, $otp);
    }

    /**
     * Generate a 6-digit OTP.
     * Returns 123456 in local/testing environment.
     */
    public function generate(): string
    {
        if (app()->environment(['local', 'testing'])) {
            return '123456';
        }

        return (string) random_int(100000, 999999);
    }

    private function normalizeMobile(string $mobile): string
    {
        // Strip non-numeric characters
        $mobile = preg_replace('/\D/', '', $mobile);

        // If it doesn't start with country code, prepend 91 (India)
        if (strlen($mobile) === 10) {
            $mobile = '91' . $mobile;
        }

        return $mobile;
    }
}
