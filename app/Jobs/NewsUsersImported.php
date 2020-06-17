<?php

namespace App\Jobs;

use App\Models\News;
use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewsUsersImported implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $newsId;

    /**
     * Create a new job instance.
     *
     * @param $newsId
     */
    public function __construct($newsId)
    {
        $this->newsId = $newsId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        News::query()->where('id', $this->newsId)->update(['user_list_imported' => true]);
    }
}
