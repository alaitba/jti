<?php

namespace App\Notifications;

use App\Models\AdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

/**
 * Class NotificationFromAdmin
 * @package App\Notifications
 */
class NotificationFromAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @param $data
     */
    public function __construct(AdminNotification $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->onesignal_token)
        {
            return [OneSignalChannel::class, 'database'];
        }
        return ['database'];
    }

    /**
     * @param $notifiable
     * @return OneSignalMessage
     */
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->setSubject($this->data->getTranslationWithFallback('title', $notifiable->locale))
            ->setBody($this->data->getTranslationWithFallback('message', $notifiable->locale))
            ->setUrl(config('project.front_url'))
            ->setIcon(config('project.push_logo', ''));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->data->only(['title', 'message']);
    }
}
