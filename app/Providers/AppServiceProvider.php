<?php

namespace App\Providers;

use App\Models\Localisation;
use App\Models\LocalisationGroup;
use App\Models\NotifyTemplate;
use App\Repositories\AdminRepository;
use App\Repositories\IAdminRepository;
use App\Repositories\IPermissionsRepository;
use App\Repositories\IRolesRepository;
use App\Repositories\PermissionsRepository;
use App\Repositories\RolesRepository;
use App\Services\LocalisationService\LocalisationService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Localisation as LocalisationContract;
use App\Contracts\LocalisationGroup as LocalisationGroupContract;
use App\Contracts\NotifyTemplate as NotifyTemplateContract;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $helper = __DIR__ . '/Helpers/Localisation.php';
        if (file_exists($helper)) {

            require_once($helper);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Repositories
         */
        $this->app->bind(IAdminRepository::class, AdminRepository::class);
        $this->app->bind(IRolesRepository::class, RolesRepository::class);
        $this->app->bind(IPermissionsRepository::class, PermissionsRepository::class);

        /**
         * Contracts
         */
        $this->app->bind(LocalisationContract::class, Localisation::class);
        $this->app->bind(LocalisationGroupContract::class, LocalisationGroup::class);
        $this->app->bind(NotifyTemplateContract::class, NotifyTemplate::class);

        /**
         * Singletons
         */
        $this->app->singleton(LocalisationService::class);

    }
}
