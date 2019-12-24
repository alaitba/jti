<?php

namespace App\Console\Commands;

use App\Imports\SalesPlan2Import;
use App\Imports\SalesPlanImport;
use App\Models\ImportHistory;
use App\Models\SalesPlan;
use App\Models\SalesPlan2;
use App\Models\SalesPlanHistory;
use App\Services\LogService\LogService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ImportFromSftp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jti:import-from-sftp {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import files from sftp';

    private $fileTypes = [
        'SalesPlan',
        'Contact',
        'Supervisor',
        'TradeAgent',
        'TradePoint',
        'TradePointContact',
        'SalesPlanHistory'
    ];

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
        $type = $this->argument('type');
        if ($type)
        {
            if (!in_array($type, $this->fileTypes))
            {
                $this->error('Неизвестный тип файла');
                return false;
            }
            return $this->processImport($type);
        }

        foreach($this->fileTypes as $type)
        {
            $this->processImport($type);
        }
        return true;
    }


    private function processImport($type)
    {
        /*
        * Check if already imported
        */
        $importHistory = ImportHistory::where('failed', 0)->where('type', $type);
        if ($type == 'SalesPlanHistory')
        {
            $importHistory->where('created_at', 'like', date('Y-m-') . '%');
        } else {
            $importHistory->whereDate('created_at', date('Y-m-d'));
        }
        if ($importHistory->count())
        {
            $this->warn($type . ($type == 'SalesPlanHistory' ? ' в этом месяце' : ' сегодня') . ' уже импортировали');
            return false;
        }

        /*
        * Download files
        */
        $today = date('dmY');
        //$today = '12122019';
        $this->info('Скачиваем ' . $type);

        $fileName = 'Trade/' . ($type === 'SalesPlanHistory' ? 'SalesPlan_history' : $type) . '+' . $today . '.csv';
        $fileName2 = false;
        try {
            Storage::disk('local')->put($fileName, Storage::disk('jti-sftp')->get($fileName));
            exec('iconv -f utf16 -t utf8 -o tmp ' . Storage::disk('local')->path($fileName));
            exec('mv tmp ' . Storage::disk('local')->path($fileName));
            if ($type == 'SalesPlan')
            {
                $fileName2 = 'Trade/SalesPlan2+' . $today . '.csv';
                Storage::disk('local')->put($fileName2, Storage::disk('jti-sftp')->get($fileName2));
                exec('iconv -f utf16 -t utf8 -o tmp ' . Storage::disk('local')->path($fileName2));
                exec('mv tmp ' . Storage::disk('local')->path($fileName2));
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            if ($e instanceof FileNotFoundException)
            {
                $msg .= ' не найден';
            }
            $this->error($msg);
            LogService::logException($e);
            $failedImport = new ImportHistory([
                'type' => $type,
                'reason' => $msg
            ]);
            $failedImport->save();
            Storage::disk('local')->delete($fileName);
            if ($type == 'SalesPlan' && $fileName2)
            {
                Storage::disk('local')->delete($fileName2);
            }
            return false;
        }
        Storage::disk('jti-sftp')->getDriver()->getAdapter()->disconnect();

        /*
         * Import
         */
        DB::beginTransaction();
        try {
            $this->info('Импортируем ' . $type);
            $timeStamp = date('Y-m-d H:i:s');
            $model = '\App\Models\\' . $type;
            if (!in_array($type, ['SalesPlan', 'SalesPlanHistory']))
            {
                $model::withoutTrashed()->update(['deleted_at' => $timeStamp]);
            }
            $importClass = '\App\Imports\\' . $type . 'Import';
            if ($type == 'SalesPlanHistory')
            {
                $baseImport = new $importClass(SalesPlanHistory::count() == 0);
            } else {
                $baseImport = new $importClass();
            }
            $baseImport->withOutput($this->output)->import(Storage::disk('local')->path($fileName), null, Excel::CSV);

            if ($type == 'SalesPlan')
            {
                $this->info('Импортируем SalesPlan2');
                (new SalesPlan2Import())->withOutput($this->output)->import(Storage::disk('local')->path($fileName2), null, Excel::CSV);
                $this->info('Импорт завершен');
                $this->info('Удаляем старые записи');
                $deleted = $model::whereDate('created_at', '<', date('Y-m-d'))->delete();
                SalesPlan2::whereDate('created_at', '<', date('Y-m-d'))->delete();
            } elseif ($type == 'SalesPlanHistory') {
                $deleted = 0;
            } else {
                $deleted = $model::onlyTrashed()->where('deleted_at', $timeStamp)->count();
            }
            $importSuccess = new ImportHistory([
                'type' => $type,
                'failed' => 0,
                'added' => $baseImport->getAdded(),
                'updated' => $baseImport->getUpdated(),
                'deleted' => $deleted
            ]);
            $importSuccess->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $msg = $e->getMessage();
            $this->warn($msg);
            LogService::logException($e);
            $failedImport = new ImportHistory([
                'type' => $type,
                'reason' => $msg
            ]);
            $failedImport->save();
        } finally {
            Storage::disk('local')->delete($fileName);
            if ($type == 'SalesPlan' && $fileName2)
            {
                Storage::disk('local')->delete($fileName2);
            }
        }
        return true;
    }

}
