<?php

namespace App\Jobs;


use App\Models\Contact;
use App\Notifications\BalanceReplenished;
use App\Notifications\LeadCreated;
use App\Notifications\LeadEffective;
use App\Notifications\LeadQualified;
use \Spatie\WebhookClient\ProcessWebhookJob as SpatieProcessWebhookJob;

class ProcessWebhookJob extends SpatieProcessWebhookJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sellerId = $this->webhookCall->payload['data']['sellerId'] ?? null;
        if (!$sellerId)
        {
            return;
        }
        $contact = Contact::withoutTrashed()->where('contact_uid', $sellerId)->first();
        if (!$contact)
        {
            return;
        }

        switch ($this->webhookCall->payload['action'])
        {
            case 'leadQualified':
                $notification = new LeadQualified($this->webhookCall->payload['data']);
                break;
            case 'leadEffective':
                $notification = new LeadEffective($this->webhookCall->payload['data']);
                break;
            case 'balanceReplenished':
                $notification = new BalanceReplenished($this->webhookCall->payload['data']);
                break;
            default:
                return;
        }
        $contact->partner->notify($notification);
    }
}
