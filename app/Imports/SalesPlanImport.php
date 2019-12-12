<?php

namespace App\Imports;

use App\Models\SalesPlan;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SalesPlanImport implements ToModel, WithProgressBar, WithChunkReading, WithHeadingRow, WithCustomCsvSettings, WithBatchInserts
{
    use Importable;

    private $rows = 0;

    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        ++$this->rows;
        return new SalesPlan([
            'account_code' => $row['Account code'],
            'bonus_portfolio' => $row['Bonus Portfolio'],
            'bonus_brand' => $row['Bonus Brand'],
            'plan_portfolio' => $row['Plan Portfolio'],
            'plan_brand' => $row['Plan Brand'],
            'fact_portfolio' => $row['Fact Portfolio'],
            'fact_brand' => $row['Fact Brand'],
        ]);
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
    public function getRowCount(): int
    {
        return $this->rows;
    }
}
