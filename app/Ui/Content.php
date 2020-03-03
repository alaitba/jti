<?php namespace App\Ui;

use App\Ui\Components\Form\OptionsFieldset;
use Throwable;

/**
 * Class Content
 * Данный класс хранит в себе конечные компоненты,
 * такие как таблицы, портлеты, формы, модалки, табы и тд
 * @package App\Ui
 */
abstract class Content
{
    /**
     * Хранилище компонентов
     * @var array
     */
    protected $components = [];

    /**
     * @var
     */
    protected $options;

    /**
     * Добавление компонента
     * @param array $components
     */
    public function setContent(array $components): void
    {
        $this->pushComponent($components);
    }

    /**
     * @param OptionsFieldset $optionsFieldset
     */
    public function setOptions(OptionsFieldset $optionsFieldset): void
    {
        $this->options = $optionsFieldset;
    }

    /**
     * @return bool
     */
    public function hasOptions(): bool
    {
        return (bool) $this->options;
    }

    /**
     * @return string
     */
    public function getOptionsTitle(): string
    {
        return $this->options->getTitle();
    }


    /**
     * Рендер компонента
     * @throws Throwable
     */
    public function getContent()
    {

        switch (get_class($this))
        {
            case 'App\Ui\Components\Portlet\Portlet':
                return view('ui.components.portlet', ['portlet' => $this]);
                break;

            case 'App\Ui\Components\Table\AjaxLoadableTable':
                return view('ui.components.ajaxableTable', ['ajaxTable' => $this]);
                break;

            case 'App\Ui\Components\Modal\Modal':
                return view('ui.components.modal_body', ['modal' => $this])->render();
                break;

            case 'App\Ui\Components\Form\Form':
                return view('ui.components.form', ['form' => $this])->render();
                break;

            case 'App\Ui\Components\Blade\Blade':
                return view($this->getView(), $this->getData())->render();
                break;


        }
        return '';
    }

    /**
     * Получение списка компонентов
     * @return array
     */
    public function getComponents():array
    {
        return $this->components;
    }

    /**
     * @return array
     */
    public function getOptionsComponents(): array
    {
        return $this->options->getComponents();
    }

    /**
     * Добавление набора компонентов в хранилище
     * @param array $components
     */
    private function pushComponent(array $components): void
    {
        array_push($this->components, $components);
    }




}
