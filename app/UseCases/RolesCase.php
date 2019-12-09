<?php

namespace App\UseCases;

use App\Repositories\IRolesRepository;

/**
 * Class RolesCase
 * @package App\UseCases
 */
class RolesCase extends BaseCase
{
    /**
     * RolesCase constructor.
     * @param IRolesRepository $rolesRepository
     */
    public function __construct(IRolesRepository $rolesRepository)
    {
        $this->repository = $rolesRepository;
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
