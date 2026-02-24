<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\OtpService;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to the given mobile number.
     * Expects: { "mobile": "9876543210" }
     */
    public function send(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|min:10|max:15',
        ]);

        $mobile = $request->input('mobile');
        $otp    = $this->otpService->generate();

        // Cache OTP for 10 minutes
        Cache::put("otp_{$mobile}", $otp, now()->addMinutes(10));

        $sent = $this->otpService->send($mobile, $otp);

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            // Return OTP only in local/testing for convenience
            'otp'     => app()->environment(['local', 'testing']) ? $otp : null,
        ]);
    }

    /**
     * Verify OTP for the given mobile number.
     * Expects: { "mobile": "9876543210", "otp": "123456" }
     */
    public function verify(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|min:10|max:15',
            'otp'    => 'required|string|size:6',
        ]);

        $mobile      = $request->input('mobile');
        $otp         = $request->input('otp');
        $cachedOtp   = Cache::get("otp_{$mobile}");

        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or not found. Please request a new one.',
            ], 422);
        }

        if ($cachedOtp !== $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        // OTP verified — remove from cache
        Cache::forget("otp_{$mobile}");

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
        ]);
    }
}
