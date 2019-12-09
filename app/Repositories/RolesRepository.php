<?php

namespace App\Repositories;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

/**
 * Class RolesRepository
 * @package App\Repositories
 */
class RolesRepository extends BaseRepository implements IRolesRepository
{
    /**
     * @var string
     */
    protected $model = Role::class;

    /**
     * @return LengthAwarePaginator
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function filterForApi(): LengthAwarePaginator
    {
        $filteredMOdel = $this->filterModel();

        return $filteredMOdel->paginate(15);
    }

    /**
     * @return object
     * @throws Exception
     * @author Rishat Sultanov
     */
    private function filterModel()
    {
        $model = $this->getModel();

        if (count($this->orderParams)) {
            foreach ($this->orderParams as $order) {
                $model = $model->orderBy($order['field'], $order['condition']);
            }
        }

        if (count($this->whereParams)) {

            foreach ($this->whereParams as $where) {
                $model = $model->where($where['field'], $where['operator'], $where['value']);
            }
        }

        if (count($this->whereBetweenParams)) {
            foreach ($this->whereBetweenParams as $params) {
                $model = $model->whereBetween($params['field'], [$params['value1'], $params['value2']]);
            }
        }

        if (count($this->relations)) {
            $model = $model->with($this->relations);
        }

        if (count($this->relationsCount)) {
            $model = $model->withCount($this->relationsCount);
        }

        return $model;
    }

    /**
     * Получение модели
     * @return object
     * @throws Exception
     */
    public function getModel()
    {
        if (!$this->model) {
            throw new Exception('Model not specified');
        }

        return new $this->model;
    }
}
