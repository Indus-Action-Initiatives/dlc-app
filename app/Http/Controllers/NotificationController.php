<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserDevice; 

class NotificationController extends Controller
{
    public function saveToken(Request $request)
    {
        try {
            // 1. Validate the incoming payload
            $request->validate([
                'deviceId'    => 'required|string',
                'fcmToken'    => 'required|string',
                'userType'    => 'nullable|string',
                'currentUser' => 'nullable|string',
            ]);

            // 2. Sync with the Postgres database
            // This silently updates the token if the device exists, or creates it if it's new.
            UserDevice::updateOrCreate(
                ['device_id' => $request->deviceId],
                [
                    'fcm_token'    => $request->fcmToken,
                    'user_type'    => $request->userType,
                    'current_user' => $request->currentUser,
                ]
            );

            Log::info("Device sync successful for: " . $request->deviceId);

            return response()->json([
                'success' => true,
                'message' => 'Device synced successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error("NotificationController Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}