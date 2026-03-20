<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserDevice; 
use App\Services\NotificationService;

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
                [
                    'device_id'   => $request->deviceId,
                    'user_id'=> $request->currentUser, // 👈 add this
                ],
                [
                    'fcm_token' => $request->fcmToken,
                    'user_type' => $request->userType,
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


    public function sendPushNotificationForChat(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'receiverId' => 'required|string',
            'senderId' => 'nullable|string',
	        'senderName'=>'nullable|string',
        ]);

        app(NotificationService::class)->send(
            [(string) $request->receiverId],
            "New message",
            (string) $request->text,
            [
                "type" => "chat",
                "receiverId" => (string) $request->receiverId,
                "senderId" => (string) $request->senderId,
                "senderName" => (string) $request->senderName,
            ]
        );
        return response()->json(['success' => true, 'message' => 'Push notification sent successfully']);
    }

}