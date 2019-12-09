<?php

namespace App\Repositories;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class AdminRepository extends BaseRepository implements IAdminRepository
{
    /**
     * @var string
     */
    protected $model = Admin::class;

    /**
     * @param array $credentials
     * @return bool
     * @author Rishat Sultanov
     */
    public function auth(array $credentials): bool
    {
      return Auth::guard('admins')->attempt($credentials);
    }

    public function filterForApi(): LengthAwarePaginator
    {
        $filteredModel = $this->filterModel();

        return $filteredModel->paginate(10);
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
}
