<?php

namespace App\Imports;

use App\Models\SalesPlan2;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

/**
 * Class SalesPlan2Import
 * @package App\Imports
 */
class SalesPlan2Import implements ToModel, WithProgressBar, WithChunkReading, WithHeadingRow, WithCustomCsvSettings, WithBatchInserts
{
    use Importable;

    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        return new SalesPlan2([
            'account_code' => $row['Account code'],
            'dsd_till_date' => $row['DSD till Date'],
            'brand' => $row['Brand']
        ]);
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            //'input_encoding' => 'UTF16LE',
            'delimiter' => "\t",
        ];
    }

}
