<?php

namespace App\Services;

use App\Models\UserDevice;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class NotificationService
{

    /**
     * Generic send method
     */
    public function send(array $userIds, $title, $body, $data = []): void
    {
        if ($userIds === []) {
            return;
        }

        $ids = array_map('strval', array_unique($userIds));

        $tokens = UserDevice::query()
            ->whereIn('user_id', $ids)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->unique()
            ->filter()
            ->values()
            ->all();

        if ($tokens === []) {
            return;
        }

        try {
            $messaging = app('firebase.messaging');
        } catch (\Throwable $e) {
            Log::error('FCM not configured: ' . $e->getMessage());
            return;
        }

        $message = CloudMessage::new()
                    ->withNotification(FcmNotification::create($title, $body))
                        ->withData($data);

        try {
            $messaging->sendMulticast($message, $tokens);

            Log::info('FCM batch send successful: ' . count($tokens) . ' messages sent');
        } catch (\Throwable $e) {
            Log::error('FCM batch send failed: ' . $e->getMessage());
        }
    }
}