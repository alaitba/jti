<?php namespace App\Ui\Components\Blade;

use App\Ui\Content;

class Blade extends Content
{
    private $viewFile;
    private $data = [];

    public function __construct(string $view, array $data)
    {
        $this->viewFile = $view;
        $this->data = $data;
    }

    public function getView()
    {
        return $this->viewFile;
    }

    public function getData()
    {
        return $this->data;
    }
}
