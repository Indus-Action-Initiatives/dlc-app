<?php

namespace App\Services;

use App\Contracts\SMSProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91SMSProvider implements SMSProvider
{
    public function send(string $mobile, string $otp): bool
    {
        $authKey    = config('msg91.auth_key');
        $templateId = config('msg91.template_id');
        $baseUrl    = config('msg91.base_url');

        if (empty($authKey)) {
            Log::error('MSG91_AUTH_KEY is not set.');
            return false;
        }

        if (empty($baseUrl)) {
            Log::error('MSG91_BASE_URL is not set.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'authkey'      => $authKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl, [
                'template_id' => $templateId,
                'mobile'      => $mobile,
                'otp'         => $otp,
            ]);
            Log::info($response->body());
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
}