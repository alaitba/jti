<?php namespace App\Ui\Components\Form;

/**
 * Class Factory
 * @package App\Ui\Components\Form
 */
class Factory
{

    /**
     * Создание филдсета
     * @param string $title
     * @param bool $tabeable
     * @return \App\Ui\Components\Form\Fieldset
     */
    public function fieldSet(string $title, $tabeable = true): Fieldset
    {
        return new Fieldset($title, $tabeable);
    }

    /**
     * Создание инпута
     * @param string $label
     * @param string $name
     * @param string $type
     * @param bool $tabeable
     * @param bool $localeable
     * @param bool $disableable
     * @return \App\Ui\Components\Form\Input
     */
    public function input(string $label, string $name, string $type = "text", bool $tabeable = true, bool $localeable = true, bool $disableable = false): Input
    {
        return new Input($label, $name, $type, $tabeable, $localeable, $disableable);
    }

    /**
     * Создание textare
     * @param string $label
     * @param string $name
     * @param bool $editorable
     * @return \App\Ui\Components\Form\Textarea
     */
    public function textarea(string $label, string $name, bool $editorable = false): Textarea
    {
        return new Textarea($label, $name, $editorable);
    }

    /**
     * Создание чекбокса
     * @param string $label
     * @param string $name
     * @return \App\Ui\Components\Form\Checkbox
     */
    public function checkbox(string $label, string $name): Checkbox
    {
        return new Checkbox($label, $name);
    }

    /**
     * Создание филдсета для опций
     * @param string $title
     * @return \App\Ui\Components\Form\OptionsFieldset
     */
    public function optionsFieldSet(string $title): OptionsFieldset
    {
        return new OptionsFieldset($title);
    }

    /**
     * Создание формы
     * @param string $method
     * @param string $action
     * @param string $submitButtonText
     * @param string $id
     * @return \App\Ui\Components\Form\Form
     */
    public function form(string $method, string $action, string $submitButtonText, string $id): Form
    {
        return new Form($method, $action,  $submitButtonText, $id);
    }

}
