<?php

namespace App\UseCases;

use App\Repositories\IAdminRepository;

/**
 * Class AdminCase
 * @package App\UseCases
 */
class AdminCase extends BaseCase
{
    /**
     * AdminCase constructor.
     * @param IAdminRepository $adminRepository
     */
    public function __construct(IAdminRepository $adminRepository)
    {
        $this->repository = $adminRepository;
    }

    /**
     * @param array $credentials
     * @return mixed
     * @author Rishat Sultanov
     */
    public function auth(array $credentials)
    {
        return $this->repository->auth($credentials);
    }

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function getListForApi()
    {
        return $this->repository->filterForApi();
    }
}
