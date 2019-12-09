<?php

namespace App\UseCases;

/**
 * Class BaseCase
 * @package App\UseCases
 */
class BaseCase
{
    /**
     * @var $repository
     */
    protected $repository;

    /**
     * @param array $data
     * @return mixed
     * @author Rishat Sultanov
     */
    public function store(array $data)
    {
        return $this->repository->store($data);
    }

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function getList()
    {
        return $this->repository->order('created_at', 'desc')->filter();
    }

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function getCollection()
    {
        return $this->repository->list();
    }

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function getTreeCollection()
    {
        return $this->repository->treeDefaultOrder()->list();
    }

    /**
     * @param array $relations
     * @return $this
     * @author Rishat Sultanov
     */
    public function with(array $relations)
    {
        $this->repository->with($relations);

        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     * @author Rishat Sultanov
     */
    public function withCount(array $relations)
    {
        $this->repository->withCount($relations);

        return $this;
    }

    /**
     * @param int $id
     * @param array $relations
     * @return mixed
     * @author Rishat Sultanov
     */
    public function item(int $id, array $relations = [])
    {
        return $this->repository->with($relations)->withCount($relations)->getById($id);
    }

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function first()
    {
       return $this->repository->first();
    }

    /**
     * @param string $field
     * @param string $condition
     * @return $this
     * @author Rishat Sultanov
     */
    public function order(string $field, string $condition)
    {
        $this->repository->order($field, $condition);

        return $this;
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @author Rishat Sultanov
     */
    public function update(int $id, array $data)
    {
        return $this->repository->updateById($id, $data);
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @author Rishat Sultanov
     */
    public function where(string $column, string $value)
    {
        $this->repository->whereEqual($column, $value);

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereNull(string $column)
    {
        $this->repository->whereNull($column);

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereMore(string $column, string $value)
    {
        $this->repository->whereMore($column, $value);

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereLess(string $column, string $value)
    {
        $this->repository->whereLess($column, $value);

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereMoreOrEqual(string $column, string $value)
    {
        $this->repository->whereMoreOrEqual($column, $value);

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereLessOrEqual(string $column, string $value)
    {
        $this->repository->whereLessOrEqual($column, $value);

        return $this;
    }

    /**
     * @param string $column
     * @param string $value1
     * @param string $value2
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereBetween(string $column, string $value1, string $value2)
    {
        $this->repository->whereBetween($column, $value1, $value2);

        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereIn(string $column, array $values)
    {
        $this->repository->whereIn($column, $values);

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     * @author Rishat Sultanov
     */
    public function whereLike(string $column, string $value)
    {
        $this->repository->whereLike($column, $value);

        return $this;
    }

    /**
     * @param int $id
     * @author Rishat Sultanov
     */
    public function treeUp(int $id)
    {
        $item = $this->item($id);

        if ($item) {
            $item->up();
        }
    }

    /**
     * @param int $id
     * @author Rishat Sultanov
     */
    public function treeDown(int $id)
    {
        $item = $this->item($id);

        if ($item) {
            $item->down();
        }
    }
}
