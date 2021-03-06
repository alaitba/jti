<?php

namespace App\Imports;

use App\Models\AdminNotification;
use App\Models\Partner;
use App\Notifications\NotificationFromAdmin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Class CustomSubscribers
 * @package App\Imports
 */
class CustomSubscribers implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{
    use Queueable;

    private $adminNotification;

    /**
     * CustomSubscribers constructor.
     * @param AdminNotification $adminNotification
     */
    public function __construct(AdminNotification $adminNotification)
    {
        $this->adminNotification = $adminNotification;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $partnerMobiles = [];
        foreach ($collection as $item) {
            $partnerMobiles []= $item['Mobile'];
        }
        $partners = Partner::withoutTrashed()
            //->whereNotNull('onesignal_token')
            ->whereIn('mobile_phone', $partnerMobiles)
            ->get();
        Notification::send($partners, new NotificationFromAdmin($this->adminNotification));
    }

    /**
     * @inheritDoc
     */
    public function chunkSize(): int
    {
        return 200;
    }
}
