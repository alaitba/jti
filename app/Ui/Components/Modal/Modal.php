<?php namespace App\Ui\Components\Modal;

use App\Ui\Content;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Throwable;

/**
 * Class Modal
 * @package App\Ui\Components\Modal
 */
class Modal extends Content {

    private $title = null;
    private $modal = null;


    /**
     * Modal constructor.
     * @param string $title
     * @param string $modal
     */
    public function __construct(string $title, string $modal)
    {
        $this->title = $title;
        $this->modal = $modal;
    }

    /**
     * @return string
     */
    public function getModal(): string
    {
        return $this->modal;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Factory|View
     * @throws Throwable
     */
    public function renderContent():string
    {
        return $this->getContent();
    }
}
