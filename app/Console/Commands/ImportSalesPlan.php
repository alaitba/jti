<?php

namespace App\Console\Commands;

use App\Imports\SalesPlan2Import;
use App\Imports\SalesPlanImport;
use App\Models\ImportHistory;
use App\Models\SalesPlan;
use App\Services\LogService\LogService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ImportSalesPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jti:import-sales-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import sales plan';

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
        /*
         * Check if already imported
         */

        if (ImportHistory::where('failed', 0)->whereDate('created_at', date('Y-m-d'))->count())
        {
            $this->error('Сегодня уже импортировали');
            return false;
        }

        /*
         * Download files
         */
        $today = date('dmY');
        //$today = '04122019';
        $this->info('Скачиваем SalesPlan и SalesPlan2');
        $file1 = 'Trade/SalesPlan+' . $today . '.csv';
        $file2 = 'Trade/SalesPlan2+' . $today . '.csv';
        try {
            Storage::disk('local')->put($file1, Storage::disk('jti-sftp')->get($file1));
            Storage::disk('local')->put($file2, Storage::disk('jti-sftp')->get($file2));
            //FIXME: убрать этот костыль, если они поправят кодировку файлов, либо сделать нормально
            //UPD: не поправят. переделать (но проверить, может не надо уже в новых версиях пыха)
            exec('iconv -f utf16 -t utf8 -o sp1 ' . Storage::disk('local')->path($file1));
            exec('iconv -f utf16 -t utf8 -o sp2 ' . Storage::disk('local')->path($file2));
            exec('mv sp1 ' . Storage::disk('local')->path($file1));
            exec('mv sp2 ' . Storage::disk('local')->path($file2));
        } catch (FileNotFoundException $e) {
            $msg = $e->getMessage() . ' не найден';
            $this->warn($msg);
            LogService::logException($e);
            $failedImport = new ImportHistory([
                'type' => 'SP',
                'reason' => $msg
            ]);
            $failedImport->save();
            Storage::disk('local')->delete($file1);
            Storage::disk('local')->delete($file2);
            return false;
        }

        /*
         * Import
         */
        try {
            $this->info('Импортируем SalesPlan');
            $baseImport = new SalesPlanImport();
            $baseImport->withOutput($this->output)->import(Storage::disk('local')->path($file1), null, Excel::CSV);

            $this->info('Импортируем SalesPlan2');
            (new SalesPlan2Import())->withOutput($this->output)->import(Storage::disk('local')->path($file2), null, Excel::CSV);
            $this->info('Импорт завершен');
            $this->info('Удаляем старые записи');
            $deleted = SalesPlan::whereDate('created_at', '<', date('Y-m-d'))->delete();
            $importSuccess = new ImportHistory([
                'type' => 'SP',
                'failed' => 0,
                'added' => $baseImport->getRowCount(),
                'deleted' => $deleted
            ]);
            $importSuccess->save();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->warn($msg);
            LogService::logException($e);
            $failedImport = new ImportHistory([
                'type' => 'SP',
                'reason' => $msg
            ]);
            $failedImport->save();
        } finally {
            Storage::disk('local')->delete($file1);
            Storage::disk('local')->delete($file2);
        }
        return true;
    }
}
