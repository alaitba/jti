<?php namespace App\Ui\Components\Table\Columns;


/**
 * Создание колонки с картинкой
 * Class ImageColumn
 * @package App\Ui\Components\Table\Columns
 */
class ImageColumn extends Column
{
    /**
     * ImageColumn constructor.
     * @param string $field
     * @param string $align
     */
    public function __construct(string $field, string $align = 'text-left')
    {
        $this->type = "image";
        $this->field = $field;
        $this->align = $align;
    }
}
