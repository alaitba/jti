<?php

namespace App\Exports;

use App\Models\Partner;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubscribedPartners implements FromCollection, ShouldAutoSize, WithHeadings
{
    use Exportable;
    /**
     * @return Collection
     */
    public function collection()
    {
        return Partner::withoutTrashed()->whereNotNull('onesignal_token')->get(['mobile_phone', 'current_uid', 'current_tradepoint', 'platform']);
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return ['Mobile', 'Seller ID', 'Account Code', 'OS'];
    }
}
