<?php

namespace App\Jobs;


use App\Models\Contact;
use App\Models\Partner;
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

        $contact->partner->notify(new LeadQualified());
    }
}
