<?php

namespace App\Imports;

use App\Models\SalesPlanHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

/**
 * Class SalesPlanHistoryImport
 * @package App\Imports
 */
class SalesPlanHistoryImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings
{
    use Importable;

    private $added = 0, $updated = 0, $full = false;

    /**
     * SalesPlanHistoryImport constructor.
     * @param $full
     */
    public function __construct($full)
    {
        $this->full = $full;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $add = [];
        foreach ($rows as $row) {
            $yearMonth = Carbon::createFromFormat('Y / m', $row['Year / Month'])->startOfMonth();
            if (!$this->full && $yearMonth->lessThan(Carbon::now()->startOfMonth()->subMonths(3)))
            {
                continue;
            }
            $yearMonth = $yearMonth->format('Y-m-d');
            $salesPlanHistory = SalesPlanHistory::query()->where(['account_code' => $row['Account code'], 'year_month' => $yearMonth])->first();
            if ($salesPlanHistory)
            {
                $salesPlanHistory->fill([
                    'bonus_portfolio' => $row['Bonus Portfolio'],
                    'bonus_brand' => $row['Bonus Brand'],
                    'plan_portfolio' => $row['Plan Portfolio'],
                    'plan_brand' => $row['Plan Brand'],
                    'fact_portfolio' => $row['Fact Portfolio'],
                    'fact_brand' => $row['Fact Brand'],
                ]);
                if ($salesPlanHistory->isDirty())
                {
                    $this->updated++;
                    $salesPlanHistory->save();
                }
            } else {
                $add []= [
                    'account_code' => $row['Account code'],
                    'year_month' => $yearMonth,
                    'bonus_portfolio' => $row['Bonus Portfolio'],
                    'bonus_brand' => $row['Bonus Brand'],
                    'plan_portfolio' => $row['Plan Portfolio'],
                    'plan_brand' => $row['Plan Brand'],
                    'fact_portfolio' => $row['Fact Portfolio'],
                    'fact_brand' => $row['Fact Brand'],
                ];
                $this->added++;
            }
        }
        SalesPlanHistory::query()->insert($add);

    }


    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 5000;
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
