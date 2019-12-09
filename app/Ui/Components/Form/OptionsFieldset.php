<?php


namespace App\Ui\Components\Form;

/**
 * Class OptionsFieldset
 * @package App\Ui\Components\Form
 */
class OptionsFieldset extends Component
{
    /**
     * @var array
     */
    private $components = [];


    /**
     * OptionsFieldset constructor.
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @param \App\Ui\Components\Form\Checkbox $checkbox
     */
    public function addChecbox(Checkbox $checkbox): void
    {
       $this->addComponent($checkbox);
    }

    /**
     * @param \App\Ui\Components\Form\Input $input
     */
    public function addInput(Input $input):void
    {
        $this->addComponent($input);
    }

    /**
     * @param object $component
     */
    private function addComponent(object $component): void
    {
        array_push($this->components, $component);
    }

}
