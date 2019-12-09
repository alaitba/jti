<?php namespace App\Ui\Components\Portlet;

use App\Ui\Content;

/**
 * Компонент портлета
 * Class Portlet
 * @package App\Ui\Components\Portlet
 */
class Portlet extends Content
{
    private $title = null;
    private $icon = null;
    private $controls = [];


    /**
     * Portlet constructor.
     * @param string $title
     * @param string $icon
     */
    public function __construct(string $title, string $icon)
    {
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     * Добавление контрола управления ввиде иконки, которая открывает модалку
     * @param string $modal
     * @param string $url
     * @param string $icon
     * @param string|null $tooltipText
     * @return \App\Ui\Components\Portlet\ModalableIconButton
     */
    public function addModalableIconButton(string $modal, string $url, string $icon, string $tooltipText = null): ModalableIconButton
    {
        $button = new ModalableIconButton($modal, $url, $icon, $tooltipText);
        $this->controls[] = $button;
        return $button;
    }


    /**
     * Добавление контрола управления в виде ссылочной иконки
     * @param string $url
     * @param string $icon
     * @param string|null $tooltipText
     */
    public function addUrlableIconButoon(string $url, string $icon, string $tooltipText = null): void
    {
        $this->controls[] = new UrlableIconButton($url, $icon, $tooltipText);
    }

    /**
     * Получение заголовка портлета
     * @return string|null
     */
    public function getTitle():string
    {
        return $this->title;
    }

    /**
     * Получение иконки портлета
     * @return string
     */
    public function getIcon(): string
    {
        return '<span class="m-portlet__head-icon"><i class="'. $this->icon .'"></i></span>' ;
    }

    /**
     * Получение списка контролов управления
     * @return array
     */
    public function getControls(): array
    {
        return $this->controls;
    }









}
