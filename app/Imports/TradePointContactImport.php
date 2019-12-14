<?php

namespace App\Imports;

use App\Models\Supervisor;
use App\Models\TradeAgent;
use App\Models\TradePoint;
use App\Models\TradePointContact;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class TradePointContactImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings, WithBatchInserts
{
    use Importable;

    private $added = 0, $updated = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $tradePointContact = TradePointContact::withTrashed()->where(['account_code' => $row['Account code'], 'contact_code' => $row['Contact code']])->first();
            if ($tradePointContact)
            {
                $tradePointContact->restore();
                $tradePointContact->fill([
                    'contact_uid' => $row['Contact UID'],
                ]);
                if ($tradePointContact->isDirty())
                {
                    $this->updated ++;
                }
                $tradePointContact->save();
            } else {
                $tradePointContact = new TradePointContact([
                    'account_code' => $row['Account code'],
                    'contact_code' => $row['Contact code'],
                    'contact_uid' => $row['Contact UID'],
                ]);
                $tradePointContact->save();
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
