<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotifications(Request $request)
    {
        try {
            $fromDateTime = Carbon::parse($request->input('from_date'));
        } catch (\Exception $e) {
            $fromDateTime = now()->subYear();
        }
        $notifications = auth('partners')->user()->notifications()
            ->where('created_at', '>', $fromDateTime)->get(['type', 'data', 'created_at'])->map(function (DatabaseNotification $notification) {
                $notification->setAttribute('type', str_replace('App\\Notifications\\', '', $notification->type));
                if (isset($notification->data['sellerId']))
                {
                    $data = $notification->getAttribute('data');
                    unset($data['sellerId']);
                    $notification->setAttribute('data', $data);
                }
                return $notification;
            });

        return response()->json([
            'status' => 'ok',
            'data' => $notifications->toArray()
        ]);
    }
}
