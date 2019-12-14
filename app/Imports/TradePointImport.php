<?php

namespace App\Imports;

use App\Models\Supervisor;
use App\Models\TradeAgent;
use App\Models\TradePoint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class TradePointImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings, WithBatchInserts
{
    use Importable;

    private $added = 0, $updated = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $tradePoint = TradePoint::withTrashed()->where(['employee_code' => $row['Employee code'], 'account_code' => $row['Account code']])->first();
            if ($tradePoint)
            {
                $tradePoint->restore();
                $tradePoint->fill([
                    'account_name' => $row['Account name'],
                    'street_address' => $row['Street address'],
                    'city' => $row['City']
                ]);
                if ($tradePoint->isDirty())
                {
                    $this->updated ++;
                }
                $tradePoint->save();
            } else {
                $tradePoint = new TradePoint([
                    'employee_code' => $row['Employee code'],
                    'account_code' => $row['Account code'],
                    'account_name' => $row['Account name'],
                    'street_address' => $row['Street address'],
                    'city' => $row['City']
                ]);
                $tradePoint->save();
                $this->added++;
            }
        }
    }


    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 2000;
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 2000;
    }

    /**
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            //'input_encoding' => 'UTF-16LE',
            'delimiter' => "\t",
        ];
    }

    public function getAdded()
    {
        return $this->added;
    }

    public function getUpdated()
    {
        return $this->updated;
    }
}
