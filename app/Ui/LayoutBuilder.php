<?php namespace App\Ui;


use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class LayoutBuilder
 * @package App\Ui
 */
class LayoutBuilder
{
    /**
     * @var array
     */
    private $rows = [];

    /**
     * @param array $objects
     */
    public function addRow(array $objects): void
    {
        array_push($this->rows, $objects);
    }

    /**
     * @return Factory|View
     */
    public function build(): View
    {

        return view('ui.layout', ['rows' => $this->rows]);
    }
}
