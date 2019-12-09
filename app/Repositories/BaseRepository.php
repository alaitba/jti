<?php

namespace App\Repositories;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class BaseRepository
 * @package App\Repositories
 */
class BaseRepository implements IBaseRepository
{
    /**
     * @var null
     */
    protected $model = null;

    /**
     * @var int
     */
    protected $perPage = 25;

    /**
     * @var array
     */
    protected $whereParams = [];

    /**
     * @var array
     */
    protected $orderParams = [];

    /**
     * @var array
     */
    protected $whereInParams = [];

    /**
     * @var array
     */
    protected $whereBetweenParams = [];

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var array
     */
    protected $relationsCount = [];

    /**
     * @var bool
     */
    protected $treeDefaultOrder = false;

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function store(array $data)
    {
        return $this->getModel()->create($data);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function getById(int $id)
    {
        $model = $this->getModel();

        if (count($this->relations)) {
            $model = $model->with($this->relations);
        }

        if (count($this->relationsCount)) {
            $model = $model->withCount($this->relationsCount);
        }

        return $model->find($id);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function firstOrCreate(array $data)
    {
        $model = $this->getModel();

        return $model->firstOrCreate($data);
    }

    /**
     * @return LengthAwarePaginator
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function filter(): LengthAwarePaginator
    {
        $filteredMOdel = $this->filterModel();

        return $filteredMOdel->paginate($this->perPage);
    }

    /**
     * @return Collection
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function list(): Collection
    {
        $filteredMOdel = $this->filterModel();

        return ($this->treeDefaultOrder) ? $filteredMOdel->defaultOrder()->get()->toTree() : $filteredMOdel->get();
    }

    /**
     * @return mixed
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function first()
    {
        $filteredMOdel = $this->filterModel();

        return $filteredMOdel->first();
    }

    /**
     * @param int $id
     * @return mixed|void
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function deleteById(int $id)
    {
        $model = $this->getById($id);
        $model->delete();
    }

    /**
     * @param string $field
     * @param string $value
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function deleteByField(string $field, string $value)
    {
        $model = $this->getModel();
        $model->where($field, $value);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function updateById(int $id, array $data)
    {
        $model = $this->getById($id);
        $model->update($data);

        return $model;
    }

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @param array $data
     * @return mixed|null
     * @throws Exception
     * @author Rishat Sultanov
     */
    public function updateByField(string $fieldName, string $fieldValue, array $data)
    {
        $item = $this->whereEqual($fieldName, $fieldValue)->first();

        if ($item) {
            return $this->updateById($item->id, $data);
        }

        return null;
    }

    /**
     * @param array $relations
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function with(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     * @author Rishat Sultanov
     */
    public function withCount(array $relations)
    {
        $this->relationsCount = $relations;

        return $this;
    }

    /**
     * @param string $field
     * @param string $condition
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function order(string $field, string $condition)
    {
        $this->orderParams[] = ['field' => $field, 'condition' => $condition];

        return $this;
    }

    /**
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function treeDefaultOrder()
    {
        $this->treeDefaultOrder = true;
        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this
     * @author Rishat Sultanov
     */
    public function where(string $field, string $value)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '=', 'value' => $value];

        return $this;
    }


    /**
     * @param string $field
     * @param string $value
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereEqual(string $field, string $value)
    {

        $this->whereParams[] = ['field' => $field, 'operator' => '=', 'value' => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereNotEqual(string $field, string $value)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '!=', 'value' => $value];

        return $this;
    }

    /**
     * @param string $field
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereNotNull(string $field)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '!=', 'value' => null];

        return $this;
    }

    /**
     * @param string $field
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereNull(string $field)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '=', 'value' => null];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereMore(string $field, string $value)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '>', 'value' => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereLess(string $field, string $value)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '<', 'value' => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereMoreOrEqual(string $field, string $value)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '>=', 'value' => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereLessOrEqual(string $field, string $value)
    {
        $this->whereParams[] = ['field' => $field, 'operator' => '<=', 'value' => $value];

        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereIn(string $field, array $values)
    {
        $this->whereInParams = [$field, $values];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value1
     * @param string $value2
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereBetween(string $field, string $value1, string $value2)
    {
        $this->whereBetweenParams[] = ['field' => $field, 'value1' => $value1, 'value2' => $value2];

        return $this;
    }

    /**
     * @param string $field
     * @param string $value
     * @return $this|mixed
     * @author Rishat Sultanov
     */
    public function whereLike(string $field, string $value)
    {

        $this->whereParams[] = ['field' => $field, 'operator' => 'like', 'value' => '%' . $value . '%'];

        return $this;
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

    /**
     * @param string $model
     * @return BaseRepository
     */
    public function setModel(string $model)
    {
        $this->model = $model;
        $this->relationsCount = [];
        $this->relations = [];
        $this->whereParams = [];
        $this->orderParams = [];

        return $this;
    }
}
