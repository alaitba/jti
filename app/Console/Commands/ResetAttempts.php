<?php

namespace App\Console\Commands;

use App\Models\Partner;
use App\Services\LogService\LogService;
use Illuminate\Console\Command;

class ResetAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reset-attempts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Обнуляем попытки');
        try {
            $partners = Partner::all();
            foreach ($partners as $partner) {
                $partner->failed_auth = 0;
                $partner->auth_blocked_till = null;
                $partner->save();
            }

            $this->info('Готово!');
        } catch (Exception $e) {
            LogService::logException($e);
            $this->error($e->getMessage());
        }
    }
}
