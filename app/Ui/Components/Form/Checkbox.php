<?php namespace App\Ui\Components\Form;

/**
 * Class Checkbox
 * @package App\Ui\Components\Form
 */
class Checkbox extends Component
{
    /**
     * Checkbox constructor.
     * @param string $label
     * @param string $name
     */
    public function __construct(string $label, string $name)
    {
        $this->label = $label;
        $this->name = $name;
    }
}
