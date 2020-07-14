<?php

namespace App\Exports;

use App\Models\Partner;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;


class PartnerAuth implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $partners;

    public function __construct($partners)
    {
        $this->partners = $partners;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $items = [];
        /** @var Partner $quizResult */
        foreach ($this->partners as $partner) {
                $items [] = [
                    'name' => $partner->name,
                    'phone' => $partner->mobile_phone,
                    'account_code' => $partner->account_code,
                    'employee_name' => $partner->employee_name,
                    'login' => $partner->login,
                    'last_seen' => $partner->last_seen,
                    'os' => $partner->os,
                    'ip' => $partner->ip
                ];
        }
        return collect($items);
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return ['Ф.И.О.', 'Телефон', 'Торговая точка', 'Торговый агент', 'Последний вход', 'Последнее действие', 'OS', 'IP'];
    }

}
