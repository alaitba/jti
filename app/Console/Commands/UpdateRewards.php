<?php

namespace App\Console\Commands;

use App\Models\Reward;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use Exception;
use Illuminate\Console\Command;

/**
 * Class UpdateRewards
 * @package App\Console\Commands
 */
class UpdateRewards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jti:update-rewards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update rewards from CRM';

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
     * @return void
     */
    public function handle()
    {
        $this->info('Обновляем список призов');
        try {
            $result = JtiApiProvider::getDictionary('rewards')->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                $this->error($result['message']);
                return;
            }
            Reward::withoutTrashed()->delete();
            foreach ($result['resultObject'] as $reward)
            {
                Reward::withTrashed()->updateOrCreate(['crm_id' => $reward['rewardId']], [
                    'qty' => $reward['availableQty'],
                    'price' => $reward['priceInPoints'],
                    'name->ru' => $reward['name']
                ])->restore();
            }
            $this->info('Готово!');
        } catch (Exception $e) {
            LogService::logException($e);
            $this->error($e->getMessage());
        }
    }
}
