<?php

namespace App\Exports;

use App\Models\Partner;
use App\Models\QuizAnswer;
use App\Models\QuizResult;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;


class PartnersReport implements FromCollection, ShouldAutoSize, WithHeadings
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
        /** @var Partner $partners */
        foreach ($this->partners as $partner) {
                $items [] = [
                    'current_tradepoint' => $partner->current_tradepoint,
                    'mobile_phone' => $partner->mobile_phone,
                    'platform' => $partner->platform,
                    'created_at' => $partner->created_at,
                    'updated_at' => $partner->updated_at,
                    'status' => $partner->status
                ];
        }
        return collect($items);
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return ['Код ТТ', 'Телефон', 'OS', 'Первый вход', 'Последний вход', 'Статус'];
    }

}
