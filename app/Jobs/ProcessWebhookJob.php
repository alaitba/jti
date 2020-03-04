<?php

namespace App\Jobs;


use App\Models\Contact;
use App\Notifications\BalanceReplenished;
use App\Notifications\LeadEffective;
use App\Notifications\LeadQualified;
use App\Services\LogService\LogService;
use \Spatie\WebhookClient\ProcessWebhookJob as SpatieProcessWebhookJob;

/**
 * Class ProcessWebhookJob
 * @package App\Jobs
 */
class ProcessWebhookJob extends SpatieProcessWebhookJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        LogService::logInfo($this->webhookCall->payload);
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
        if ($contact->partner)
        {
            $contact->partner->notify($notification);
        }
    }
}
