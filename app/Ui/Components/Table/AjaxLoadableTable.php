<?php namespace App\Ui\Components\Table;

/**
 * Компонент таблицы, которая загружает свои данные после загрузки страницы
 * Class AjaxLoadableTable
 * @package App\Ui\Components\Table
 */
class AjaxLoadableTable extends Table
{
    /**
     * AjaxLoadableTable constructor.
     * @param string $url
     * @param string $id
     */
    public function __construct(string $url, string $id)
    {
        $this->url = $url;
        $this->id = $id;
    }


}
