<?php

namespace App\Imports;

use App\Models\TradePoint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

/**
 * Class TradePointImport
 * @package App\Imports
 */
class TradePointImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings, WithBatchInserts
{
    use Importable;

    private $added = 0, $updated = 0;

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $add = [];
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
                    $this->updated++;
                    $tradePoint->save();
                }
            } else {
                $add []= [
                    'employee_code' => $row['Employee code'],
                    'account_code' => $row['Account code'],
                    'account_name' => $row['Account name'],
                    'street_address' => $row['Street address'],
                    'city' => $row['City']
                ];
                $this->added++;
            }
        }
        TradePoint::query()->insert($add);
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

    /**
     * @return int
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
