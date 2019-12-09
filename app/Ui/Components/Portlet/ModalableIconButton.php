<?php namespace App\Ui\Components\Portlet;

/**
 * Иконка для портлета открывающая модальное окно
 * Class ModalableIconButton
 * @package App\Ui\Components\Portlet
 */
class ModalableIconButton extends Control
{

    /**
     * ModalableIconButton constructor.
     * @param string $modal
     * @param string $url
     * @param string $icon
     * @param string|null $tooltipText
     */
    public function __construct(string $modal, string $url, string $icon, string $tooltipText = null)
    {
        $this->modal = $modal;
        $this->url = $url;
        $this->icon = $icon;
        $this->tooltipText = $tooltipText;
    }

    /**
     * Генерация контрола
     * @return string
     */
    public function render(): string
    {
        return '<li class="m-portlet__nav-item"><a href="#" data-type="modal" data-modal="'. $this->modal .'" data-url="'. $this->url .'" class="m-portlet__nav-link m-portlet__nav-link--icon  handle-click" '. $this->getTooltip() .' >'. $this->getIcon() .'</a></li>';
    }


}
