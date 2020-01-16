<?php namespace App\Ui\Components\Blade;

use App\Ui\Content;

/**
 * Class Blade
 * @package App\Ui\Components\Blade
 */
class Blade extends Content
{
    private $viewFile;
    private $data = [];

    /**
     * Blade constructor.
     * @param string $view
     * @param array $data
     */
    public function __construct(string $view, array $data)
    {
        $this->viewFile = $view;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->viewFile;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
