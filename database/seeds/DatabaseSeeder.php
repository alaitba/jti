<?php

use Illuminate\Database\Seeder;
use App\Traits\Seedable;

class DatabaseSeeder extends Seeder
{
    use Seedable;

    protected $seedersPath = __DIR__.'/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed('RolesAndPermissionsSeeder');
    }
}
