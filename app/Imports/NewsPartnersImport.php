<?php

namespace App\Imports;

use App\Models\News;
use App\Models\NewsPartner;
use App\Models\Partner;
use App\Services\LogService\LogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class NewsPartnersImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{
    use Queueable, Importable, RegistersEventListeners;

    private $news;

    /**
     * QuizPartnersImport constructor.
     * @param News $news
     */
    public function __construct(News $news)
    {
        $this->news = $news;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $partnerMobiles = $collection->pluck('Mobile');
        $items = Partner::withoutTrashed()
            ->whereIn('mobile_phone', $partnerMobiles)
            ->get(DB::raw('id AS partner_id, ' . $this->news->id . ' AS news_id'))
            ->toArray();
        NewsPartner::query()->insertOrIgnore($items);
    }

    /**
     * @inheritDoc
     */
    public function chunkSize(): int
    {
        return 1000;
    }

}
