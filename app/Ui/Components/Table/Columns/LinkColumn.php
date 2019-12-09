<?php namespace App\Ui\Components\Table\Columns;

/**
 * Создание ссылочной колонки
 * Class LinkColumn
 * @package App\Ui\Components\Table\Columns
 */
class LinkColumn extends Column
{

    /**
     * LinkColumn constructor.
     * @param string $field
     * @param string $route
     * @param array $params
     * @param string $align
     */
    public function __construct(string $field, string $route, array $params = [], string $align = 'text-left')
    {
        $this->field = $field;
        $this->route = $route;
        $this->routeParams = $params;
        $this->align = $align;
    }


}
