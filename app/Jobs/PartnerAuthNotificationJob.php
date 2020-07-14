<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Notifications\PartnerAuthNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PartnerAuthNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 100;
    public $request;
    public $path;

    /**
     * PartnerAuthNotificationJob constructor.
     * @param $request
     * @param $path
     */
    public function __construct($request, $path)
    {
        $this->request = $request;
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = Admin::find($this->request);
        $user->notify(new PartnerAuthNotification($this->path));
    }
}
