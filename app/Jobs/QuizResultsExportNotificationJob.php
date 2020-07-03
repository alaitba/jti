<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Notifications\QuizResultsExportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QuizResultsExportNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 100;
    public $request;
    public $path;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $path)
    {
        $this->request = $request;
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $user = Admin::find($this->request);
        $user->notify(new QuizResultsExportNotification($this->path));
    }
}
