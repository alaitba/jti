<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface IBaseRepository
 * @package App\Repositories
 */
interface IBaseRepository
{
    /**
     * @param array $data
     * @return mixed
     * @author Rishat Sultanov
     */
    public function store(array $data) ;

    /**
     * Получение одной записи по ее ID
     * @param int $id
     * @return mixed
     */
    public function getById(int $id) ;

    /**
     * Получение записей или одной записи по нескольким параметрам
     * @return mixed
     */
    public function filter(): LengthAwarePaginator;

    /**
     * @return Collection
     * @author Rishat Sultanov
     */
    public function list(): Collection;

    /**
     * @param array $relations
     * @return mixed
     * @author Rishat Sultanov
     */
    public function with(array $relations);

    /**
     * @param array $data
     * @return mixed
     * @author Rishat Sultanov
     */
    public function firstOrCreate(array $data);

    /**
     * Удаление записи по ее ID
     * @param int $id
     * @return mixed
     */
    public function deleteById(int $id);

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     */
    public function deleteByField(string $field, string $value);


    /**
     * Обновление записи по ее ID
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateById(int $id, array $data);

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @param array $data
     * @return mixed
     * @author Rishat Sultanov
     */
    public function updateByField(string $fieldName, string $fieldValue, array $data);

    /**
     * @param string $field
     * @param string $condition
     * @return mixed
     * @author Rishat Sultanov
     */
    public function order(string $field, string $condition);

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function treeDefaultOrder();

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereEqual(string $field, string $value);

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereNotEqual(string $field, string $value);

    /**
     * @param string $field
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereNotNull(string $field);

    /**
     * @param string $field
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereNull(string $field);

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereMore(string $field, string $value);

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereLess(string $field, string $value);

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereMoreOrEqual(string $field, string $value);

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereLessOrEqual(string $field, string $value);

    /**
     * @param string $field
     * @param array $values
     * @return mixed
     * @author Rishat Sultanov
     */
    public function whereIn(string $field, array $values);

    /**
     * @return mixed
     * @author Rishat Sultanov
     */
    public function getModel();

    /**
     * @param string $model
     * @return mixed
     * @author Rishat Sultanov
     */
    public function setModel(string $model);
}
