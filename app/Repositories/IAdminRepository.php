<?php

namespace App\Repositories;

/**
 * Interface IAdminRepository
 * @package App\Repositories
 */
interface IAdminRepository extends IBaseRepository
{
    /**
     * @param array $credentials
     * @return mixed
     * @author Rishat Sultanov
     */
    public function auth(array $credentials);
}
