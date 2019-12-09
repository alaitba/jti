<?php namespace App\Ui\Components\Portlet;

/**
 * Class Control
 * Абстрактный класс контрола для портлета
 * @package App\Ui\Components\Portlet
 */
abstract class Control
{
    protected $title = null;
    protected $icon = null;
    protected $url = null;
    protected $modal = null;
    protected $tooltipText = null;

    /**
     * Получение тултипа
     * @return string
     */
    protected function getTooltip(): string
    {
        return ($this->tooltipText) ? 'data-container="body" data-toggle="m-tooltip" data-placement="top" title="'. $this->tooltipText .'"' : '';
    }

    /**
     * Получение иконки
     * @return string
     */
    public function getIcon(): string
    {
        return '<i class="'. $this->icon .'"></i>';
    }

}
