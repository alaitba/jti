<?php

namespace App\Jobs;

use App\Exports\QuizResults;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class QuizResultsExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $path;

    /**
     * QuizResultsExportJob constructor.
     * @param $data
     */
    public function __construct($data, $path)
    {
        $this->data = $data;
        $this->path = $path;
    }

    /**
     *
     */
    public function handle()
    {
        Excel::store(new QuizResults($this->data), $this->path);
    }
}
