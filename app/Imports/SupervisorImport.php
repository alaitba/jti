<?php

namespace App\Imports;

use App\Models\Supervisor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SupervisorImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings, WithBatchInserts
{
    use Importable;

    private $added = 0, $updated = 0;

    public function collection(Collection $rows)
    {
        $add = [];
        foreach ($rows as $row) {
            $supervisor = Supervisor::withTrashed()->where(['district_employee_code' => $row['District - Employee Code']])->first();
            if ($supervisor)
            {
                $supervisor->restore();
                $supervisor->fill([
                    'district_employee_name' => $row['District - Employee Name'],
                ]);
                if ($supervisor->isDirty())
                {
                    $this->updated++;
                    $supervisor->save();
                }
            } else {
                $add []= [
                    'district_employee_code' => $row['District - Employee Code'],
                    'district_employee_name' => $row['District - Employee Name'],
                ];
                $this->added++;
            }
        }
        Supervisor::insert($add);
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
