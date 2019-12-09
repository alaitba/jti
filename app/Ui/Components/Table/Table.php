<?php  namespace App\Ui\Components\Table;

use App\Ui\Content;

/**
 * Class Table
 * Базовый класс таблицы
 * Коллектит заголовочные колонки
 * Коллектит контентные колонки
 * Хранит данные таблицы
 * @package App\Ui\Components\Table
 */
abstract class Table extends Content
{
    /**
     * Хранилише заголовочных колонок таблицы
     * @var array
     */
    protected $columns = [];

    /**
     * Хранилище контентных классов колонок
     * @var array
     */
    protected $contentColumns = [];


    /**
     * Юрл для получения контента таблицы в случае, если таблица ajax loadable
     * @var null
     */
    protected $url = null;

    /**
     * Хранилище данных таблицы
     * @var null
     */
    protected $data = null;

    /**
     * Добавление заголовочной колонки
     * @param string $title
     * @param string $align
     * @param string|null $width
     */
    public function addColumn(string $title, string $align, string $width = null)
    {
        $column = [
            'title' => (strpos($title, 'la la') !== false || strpos($title, 'flaticon') !== false) ? '<i class="'. $title .'"></i>' : $title,
            'align' => $align,
            'width' => $width
        ];
        array_push($this->columns, $column);
    }

    /**
     * Добавление контентной колонки
     * @param object $column
     */
    public function addContentColumn(object $column)
    {
        array_push($this->contentColumns, $column);
    }

    /**
     * Получение заголовочных колонок
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Получение контентных колонок
     * @return array
     */
    public function getContentColumns()
    {
        return $this->contentColumns;
    }

    /**
     * Получение юрл
     * @return null |null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Получение айди таблицы
     * @return string
     */
    public function getId()
    {
        return ($this->id != null) ? $this->id : '';
    }


}
