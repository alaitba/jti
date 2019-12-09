<?php namespace App\Ui\Components\Table\Columns;


/**
 * Создание текстовой колонки
 * Class TextColumn
 * @package App\Ui\Components\Table\Columns
 */
class TextColumn extends Column
{
    /**
     * TextColumn constructor.
     * @param string $field
     * @param string $align
     */
    public function __construct(string $field, string $align = 'text-left')
    {
        $this->field = $field;
        $this->align = $align;
    }


}
