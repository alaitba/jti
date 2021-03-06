<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\Partner;
use App\Services\LogService\LogService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Exception;

/**
 * Class ContactImport
 * @package App\Imports
 */
class ContactImport implements ToCollection, WithHeadingRow, WithProgressBar, WithChunkReading, WithCustomCsvSettings
{
    use Importable;

    private $added = 0, $updated = 0;

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $add = [];
        $addPartners = [];
        foreach ($rows as $row) {
            $contact = Contact::withTrashed()->where(['contact_code' => $row['Contact code'], 'contact_uid' => $row['Contact ID']])->first();
            if ($contact)
            {
                $contact->restore();
                $contact->fill([
                    'contact_type' => $row['Contact type'],
                    'mobile_phone' => $row['Mobile phone #'],
                    'first_name' => $row['Contact first name'],
                    'last_name' => $row['Contact last name'],
                    'middle_name' => $row['Contact middle name'] ?? '',
                    'iin_id' => $row['IIN ID'] ?? '',
                ]);
                if ($contact->isDirty())
                {
                    $this->updated++;
                    $contact->save();
                }
            } else {
                $add []= [
                    'contact_code' => $row['Contact code'],
                    'contact_uid' => $row['Contact ID'],
                    'contact_type' => $row['Contact type'],
                    'mobile_phone' => $row['Mobile phone #'],
                    'first_name' => $row['Contact first name'],
                    'last_name' => $row['Contact last name'],
                    'middle_name' => $row['Contact middle name'] ?? '',
                    'iin_id' => $row['IIN ID'] ?? ''
                ];
                $this->added++;
            }
            $now = now();
            $addPartners []= ['mobile_phone' => $row['Mobile phone #'], 'created_at' => $now, 'updated_at' => $now];
        }
        foreach ($add as $value) {
            try {
                Contact::query()->insert($value);
            } catch (Exception $e) {
                \Sentry\captureMessage('Ошибка данных: ' . implode(', ', $value));
                LogService::logInfo(implode(', ', $value));
                LogService::logException($e);
                LogService::logInfo($e->getCode());
            }
        }
        Partner::query()->insertOrIgnore($addPartners);
    }

    /**
     * @return int
     */
    public function chunkSize(): int
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
