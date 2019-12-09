<?php

namespace App\UseCases;

use App\Repositories\IPermissionsRepository;

/**
 * Class PermissionsCase
 * @package App\UseCases
 */
class PermissionsCase extends BaseCase
{
    /**
     * PermissionsCase constructor.
     * @param IPermissionsRepository $permissionsRepository
     */
    public function __construct(IPermissionsRepository $permissionsRepository)
    {
        $this->repository = $permissionsRepository;
    }

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function getListForApi()
    {
        return $this->repository->filterForApi();
    }

    /**
     * @return string
     * @author Rishat Sultanov
     */
    public function getModelClass()
    {
        return get_class($this->repository->getModel());
    }

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function getModel()
    {
        return $this->repository->getModel();
    }
}
