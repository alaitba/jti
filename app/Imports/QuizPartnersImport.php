<?php

namespace App\Imports;

use App\Models\Partner;
use App\Models\Quiz;
use App\Models\QuizPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class QuizPartnersImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{
    use Queueable, Importable, RegistersEventListeners;

    private $quiz;

    /**
     * QuizPartnersImport constructor.
     * @param Quiz $quiz
     */
    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $partnerMobiles = $collection->only('Mobile');
        $items = [];
        Partner::withoutTrashed()
            ->whereIn('mobile_phone', $partnerMobiles)
            ->get(['id'])->each(function (Partner $partner) use (&$items) {
                $items []= [
                    'quiz_id' => $this->quiz->id,
                    'partner_id' => $partner->id
                ];
            });
        QuizPartner::query()->insert($items);
    }

    /**
     * @inheritDoc
     */
    public function chunkSize(): int
    {
        return 1000;
    }

}
