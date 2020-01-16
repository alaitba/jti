<?php namespace App\Ui\Components\Form;

/**
 * Class Textarea
 * @package App\Ui\Components\Form
 */
class Textarea extends Component
{

    /**
     * Textarea constructor.
     * @param string $label
     * @param string $name
     * @param bool $editorable
     * @param bool $localeable
     */
    public function __construct(string $label, string $name, bool $editorable = false, bool $localeable = false)
    {
        $this->label = $label;
        $this->name = $name;
        $this->editorable = $editorable;
        $this->localeable = $localeable;

    }

    /**
     * @param int $number
     * @return $this
     */
    public function setRowsNumber(int $number)
    {
        $this->textareaRowsNumber = $number;

        return $this;
    }

}
