<?php namespace App\Ui\Components\Form;

/**
 * Class Fieldset
 * @package App\Ui\Components\Form
 */
class Fieldset extends Component
{
    /**
     * @var bool
     */
    protected $tabeable = true;

    /**
     * @var array
     */
    private $components = [];


    /**
     *
     * Fieldset constructor.
     * @param string $title
     * @param bool $tabeable
     */
    public function __construct(string $title, $tabeable = true)
    {
        $this->title = $title;
        $this->tabeable = $tabeable;

    }

    /**
     * Добавление компонента
     * @param object $component
     * @return Fieldset
     */
    public function addComponent(object $component): self
    {
        array_push($this->components, $component);
        return $this;
    }

    /**
     * Получение компонентов
     * @return array
     */
    public function getComponents(): array
    {
        return $this->components;
    }


}
