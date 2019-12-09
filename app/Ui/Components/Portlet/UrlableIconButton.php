<?php namespace App\Ui\Components\Portlet;


/**
 * Class UrlableIconButton
 * Ссылочная иконка
 * @package App\Ui\Components\Portlet
 */
class UrlableIconButton extends Control
{
    /**
     * UrlableIconButton constructor.
     * @param string $url
     * @param string $icon
     * @param string|null $tooltipText
     */
    public function __construct(string $url, string $icon, string $tooltipText = null)
    {
        $this->url = $url;
        $this->icon = $icon;
        $this->tooltipText = $tooltipText;
    }

    /**
     * Генерация иконки
     * @return string
     */
    public function render(): string
    {
        return '<li class="m-portlet__nav-item"><a href="'.  $this->url .'"  class="m-portlet__nav-link m-portlet__nav-link--icon" '. $this->getTooltip() .' >'. $this->getIcon() .'</a></li>';
    }
}
