<?php

namespace App\Jobs;

use App\Exports\PartnerAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class PartnerAuthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 100;
    public $data;
    public $path;

    /**
     * PartnerAuthJob constructor.
     * @param $data
     * @param $path
     */
    public function __construct($data, $path)
    {
        $this->data = $data;
        $this->path = $path;
    }

    public function handle()
    {
        Excel::store(new PartnerAuth($this->data), $this->path);
    }
}
