<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;

class SuperUserCommand extends Command
{
    private $admin;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:add-super-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание супер юзера';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->askemail();
        $name = $this->ask('Введите имя');
        $password = $this->ask('Введите пароль');
        $develop = $this->choice('Develop?', [0, 1], 'No');

        $admin = Admin::create([
            'super_user' => 1,
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'develop' => $develop
        ]);

        $admin->assignRole('admin');

        $this->info('Супер администратор успешно добавлен.');
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
