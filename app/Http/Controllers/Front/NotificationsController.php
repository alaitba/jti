<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Reward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Class NotificationsController
 * @package App\Http\Controllers\Front
 */
class NotificationsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotifications(Request $request)
    {
        $fromDateTime = $request->input('from_date', now()->subYear());
        /** @var Partner $user */
        $user = auth('partners')->user();
        $notifications = $user->notifications()
            ->where('created_at', '>', $fromDateTime)
            ->where('type', '!=', 'App\\Notifications\\NotificationFromAdmin')
            ->get(['type', 'data', 'created_at'])->map(function (DatabaseNotification $notification) {
                if ($notification->type == 'App\\Notifications\\RewardBought')
                {
                    $data = $notification->getAttribute('data');
                    $data['rewardTitle'] = Reward::withTrashed()->where('crm_id', $data['rewardId'])->first()->name ?? 'Unknown';
                    unset($data['rewardId']);
                    $notification->setAttribute('data', $data);
                }
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
