<?php

namespace App\Imports;

use App\Models\TobaccoProduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class TobaccoProductImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings, WithBatchInserts
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
            $product = TobaccoProduct::withTrashed()->where(['product_code' => $row['Tobacco product code']])->first();
            if ($product)
            {
                $product->restore();
                $product->fill([
                    'brand' => $row['Brand'],
                    'sku' => $row['SKU']
                ]);
                if ($product->isDirty())
                {
                    $this->updated++;
                    $product->save();
                }
            } else {
                $add []= [
                    'product_code' => $row['Tobacco product code'],
                    'brand' => $row['Brand'],
                    'sku' => $row['SKU']
                ];
                $this->added++;
            }
        }
        TobaccoProduct::insert($add);

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
