<?php

namespace App\Jobs;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QuizUsersImported implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $quizId;

    /**
     * Create a new job instance.
     *
     * @param $quizId
     */
    public function __construct($quizId)
    {
        $this->quizId = $quizId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Quiz::query()->where('id', $this->quizId)->update(['user_list_imported' => true]);
    }
}
