<?php

use App\Services\LocalisationService\LocalisationService;

if (!function_exists('st_trans'))
{
    /**
     * Получем перевод
     * @param string $name
     * @return string
     */
    function st_trans(string $name): string
    {
        return app(LocalisationService::class)->getTranslation($name, app()->getLocale());
    }
}
