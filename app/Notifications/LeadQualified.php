<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;

class LeadQualified extends Notification implements ShouldQueue
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
        $leadId = $this->data['leadid'] ?? '';
        $lead = Lead::query()->where('crm_id', $leadId)->first();
        if ($lead && $lead->self)
        {
            $this->data['self'] = 1;
            return [OneSignalChannel::class, 'database'];
        }
        $this->data['self'] = 0;
        return ['database'];
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
                    'Потребитель с номером %s завершил заполнение на realday.kz и вы получаете %s баллов',
                    $this->data['mobilePhone'],
                    $this->data['amount']
                )
            )->setIcon(config('project.push_logo', ''))->setUrl('/anketa/listanketa');
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
