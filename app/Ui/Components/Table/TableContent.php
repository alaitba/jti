<?php

namespace App\Ui\Components\Table;


use Throwable;
use App\Ui\Components\Table\Columns\ImageColumn;
use App\Ui\Components\Table\Columns\TextColumn;
use App\Ui\Components\Table\Columns\LinkColumn;


/**
 * Класс формирует контент таблицы.
 * Таблица поддерживает текстовые колонки и ссылочные
 *
 * У модели может вызывать свойство, может определять булев тип поля,
 * может вызывать кастомную функцию
 *
 * Поле может содержать как текст, так и иконку.
 *
 * Поле может быть ссылкой для перехода на другую страницу, для открытия модалки,
 * а также выполнение гет запроса на бэкэнд с подтверждением.
 *
 * Class TableContent
 * @package App\Ui\Components\Table
 */
class TableContent extends Table
{


    /**
     * При создании инстанса нужно передать пагинированный набор данных
     * TableContent constructor.
     * @param object $data
     */
    public function __construct(object $data)
    {
        $this->data = $data;
    }

    /**
     * Создание текстовое колонки
     * @param string $field
     * @param string $align
     * @return TextColumn
     */
    public function textColumn(string $field, string $align = 'text-left'):TextColumn
    {
        $column = new TextColumn($field, $align);
        $this->addContentColumn($column);

        return $column;
    }

    /**
     * Создание картиночной колонки
     * @param string $field
     * @param string $align
     * @return ImageColumn
     */
    public function imageColumn(string $field, string $align = 'text-left'):ImageColumn
    {
        $column = new ImageColumn($field, $align);
        $this->addContentColumn($column);

        return $column;
    }

    /**
     * Создание ссылочной колокни
     * @param string $field
     * @param string $routeName
     * @param array $params
     * @param string $align
     * @return LinkColumn
     */
    public function linkColumn(string $field, string $routeName, array $params = [], string $align = 'text-left'): LinkColumn
    {
        $column = new LinkColumn($field, $routeName, $params, $align);
        $this->addContentColumn($column);

        return $column;
    }


    /**
     * Генерация данных таблицы в html для response()->json()
     * или для добавления в таблицу (в заголовк)
     * @return string
     * @throws Throwable
     */
    public function renderHtml(): string
    {

        switch (get_class($this->data))
        {
            case 'Illuminate\Pagination\LengthAwarePaginator':

                return view('ui.components.tableContent', [
                    'data' => $this->data,
                    'columns' => $this->getContentColumns()
                ])->render();
                break;

            default:
                return view('ui.components.tableRow', [
                    'item' => $this->data,
                    'columns' => $this->getContentColumns()
                ])->render();
                break;
        }

    }

    /**
     * Генерация пагинации в html для response()->json()
     * или для добавления под таблицей
     * @param array $filter
     * @return string
     * @throws Throwable
     */
    public function renderPagination(array $filter = []): string
    {
        return view('layouts.pagination', [
            'links' => $this->data->appends($filter)->links('pagination.bootstrap-4'),
        ])->render();
    }


}
