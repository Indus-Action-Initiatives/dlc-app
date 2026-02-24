<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Send OTP to the given mobile number via MSG91.
     * In local/testing environment, always uses 123456.
     */
    public function send(string $mobile, string $otp): bool
    {
        if (app()->environment(['local', 'testing'])) {
            Log::info("OTP (test mode) for {$mobile}: {$otp}");
            return true;
        }

        $authKey    = config('msg91.auth_key');
        $templateId = config('msg91.template_id');
        $baseUrl    = config('msg91.base_url');

        if (empty($authKey)) {
            Log::error('MSG91_AUTH_KEY is not set.');
            return false;
        }

        // Ensure mobile has country code (default +91 for India)
        $mobile = $this->normalizeMobile($mobile);

        try {
            $response = Http::withHeaders([
                'authkey'      => $authKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl, [
                'template_id' => $templateId,
                'mobile'      => $mobile,
                'otp'         => $otp,
            ]);

            if ($response->successful()) {
                Log::info("OTP sent via MSG91 to {$mobile}");
                return true;
            }

            Log::error('MSG91 OTP send failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('MSG91 OTP exception: ' . $e->getMessage());
            return false;
        }
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
