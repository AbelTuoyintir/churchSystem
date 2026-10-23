<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    protected PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Store browser push subscription.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['nullable', 'string'],
            'keys.auth' => ['nullable', 'string'],
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = $this->pushService->subscribe($user, $request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Push notifications enabled successfully!',
            'id' => $subscription->id,
        ]);
    }

    /**
     * Send test push notification to current user.
     */
    public function test(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = $this->pushService->sendToUser(
            $user,
            'Church System Notification Test',
            'Push notifications are working perfectly on this device! You will now receive important updates even when offline.',
            route('members.portal')
        );

        return response()->json([
            'status' => 'success',
            'notified_endpoints' => $count,
            'message' => 'Test notification dispatched!',
        ]);
    }
}
