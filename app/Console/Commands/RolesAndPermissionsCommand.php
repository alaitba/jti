<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Traits\Seedable;

class RolesAndPermissionsCommand extends Command
{
    use Seedable;

    /**
     * Путь к сидам.
     *
     * @var string
     */
    protected $seedersPath = __DIR__.'/../../../database/seeds/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:create-roles-and-permissions {--tty : All true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание ролей и прав';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tty = $this->option('tty');

        if ($tty) {
            $this->info('Seeding data into the database');
            $this->seed('DatabaseSeeder');
            $this->info('Роли и права созданы');
        } else {
            $this->error('Роли и права не созданы, может вы забыли ввести --tty в конце команды?');
        }
    }

    private function askemail()
    {
        $email = $this->ask('Введите email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Указанный вами e-mail имеет неверный формат');
            $email = $this->askemail();
        }

        if (Admin::where('email', $email)->count()) {
            $this->error('Администратор с таким e-mail уже существует:');
            $email = $this->askemail();
        }

        return $email;

    }
}
