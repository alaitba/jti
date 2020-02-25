<?php

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;
use App\Notifications\HappyBirthday as HappyBirthdayNotification;
/**
 * Class HappyBirthday
 * @package App\Console\Commands
 */
class HappyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jti:happy-birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send happy birthday notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Хэппибёздим');
        $birthdayContacts = Contact::withoutTrashed()->with('partner')
            ->select('mobile_phone')
            ->whereRaw('SUBSTRING(iin_id, 3, 4) = DATE_FORMAT(NOW(), "%m%d")')
            ->groupBy('mobile_phone')
            ->get();
        foreach ($birthdayContacts as $contact)
        {
            if ($contact->partner && $contact->partner->onesignal_token)
            {
                $contact->partner->notify(new HappyBirthdayNotification([]));
            }
        }
        $this->info('Готово');
    }
}
