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

/**
 * Class SalesPlanImport
 * @package App\Imports
 */
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
            'bonus_portfolio' => $row['Bonus Portfolio'] ?? 0,
            'bonus_brand' => $row['Bonus Brand'] ?? 0,
            'bonus_total' => $row['Bonus Total'] ?? 0,
            'plan_portfolio' => $row['Plan Portfolio'] ?? 0,
            'plan_brand' => $row['Plan Brand'] ?? 0,
            'fact_portfolio' => $row['Fact Portfolio'] ?? 0,
            'fact_brand' => $row['Fact Brand'] ?? 0,
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
    public function getAdded(): int
    {
        return $this->rows;
    }

    /*
     * @return int
     */
    /**
     * @return int
     */
    public function getUpdated(): int
    {
        return 0;
    }
}
