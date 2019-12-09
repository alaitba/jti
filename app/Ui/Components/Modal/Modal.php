<?php namespace App\Ui\Components\Modal;

use App\Ui\Content;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class Modal extends Content {

    private $title = null;
    private $modal = null;


    public function __construct(string $title, string $modal)
    {
        $this->title = $title;
        $this->modal = $modal;
    }

    public function getModal(): string
    {
        return $this->modal;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Factory|View
     */
    public function renderContent():string
    {
        return $this->getContent();
    }
}
