<?php

namespace App\Jobs;


use App\Models\Contact;
use App\Models\Partner;
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
        \Log::info($this->webhookCall);
        $sellerId = $this->webhookCall->payload->data->sellerId ?? null;
        if (!$sellerId)
        {
            return;
        }
        $contact = Contact::withoutTrashed()->where('contact_uid', $sellerId)->first();
        if (!$contact)
        {
            return;
        }
        $partner = Partner::withoutTrashed()->where('mobile_phone', $contact->mobile_phone)->first();
    }
}
