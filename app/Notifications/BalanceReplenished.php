<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;

class BalanceReplenished extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @param $data
     */
    public function __construct(array $data)
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
        return [OneSignalChannel::class, 'database'];
    }

    /**
     * @param $notifiable
     * @return OneSignalMessage
     */
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->setBody(
                sprintf(
                    'На Ваш номер было начислено %s тг.',
                    $this->data['amount']
                )
            )->setIcon(config('project.push_logo', ''))->setUrl('/notifications');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->data;
    }
}
