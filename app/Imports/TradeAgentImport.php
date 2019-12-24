<?php

namespace App\Imports;

use App\Models\TradeAgent;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class TradeAgentImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings, WithBatchInserts
{
    use Importable;

    private $added = 0, $updated = 0;

    public function collection(Collection $rows)
    {
        $add = [];
        foreach ($rows as $row) {
            $tradeAgent = TradeAgent::withTrashed()->where(['employee_code' => $row['Employee code']])->first();
            if ($tradeAgent)
            {
                $tradeAgent->restore();
                $tradeAgent->fill([
                    'employee_name' => $row['Employee name'],
                    'district_employee_code' => $row['District - Employee code'],
                    'phone' => $row['Cell Phone Nb'] ?? ''
                ]);
                if ($tradeAgent->isDirty())
                {
                    $this->updated++;
                }
                $tradeAgent->save();
            } else {
                $add []= [
                    'employee_code' => $row['Employee code'],
                    'employee_name' => $row['Employee name'],
                    'district_employee_code' => $row['District - Employee code'],
                    'phone' => $row['Cell Phone Nb'] ?? ''
                ];
                $this->added++;
            }
        }
        TradeAgent::insert($add);
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
