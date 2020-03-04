<?php namespace App\Ui\Components\Table\Columns;


use Exception;

/**
 * Class Column
 * @package App\Ui\Components\Table\Columns
 */
abstract class Column
{
    /**
     * @var string
     */
    protected $type = 'text';
    /**
     * @var null
     */
    protected $field = null;
    /**
     * @var null
     */
    protected $functionName = null;
    /**
     * @var null
     */
    protected $functionParams = null;
    /**
     * @var null
     */
    protected $route = null;
    /**
     * @var array
     */
    protected $routeParams = [];
    /**
     * @var null
     */
    protected $align = null;
    /**
     * @var null
     */
    protected $modal = null;
    /**
     * @var null
     */
    protected $icon = null;
    /**
     * @var null
     */
    protected $iconColor = null;
    /**
     * @var bool
     */
    protected $ajaxable = true;
    /**
     * @var null
     */
    protected $iconColorBoolean = null;
    /**
     * @var null
     */
    protected $confirmTitle = null;
    /**
     * @var null
     */
    protected $confirmMessage = null;
    /**
     * @var null
     */
    protected $confirmType = null;
    /**
     * @var null
     */
    protected $width = null;

    /**
     * @param string $functionName
     * @param array $params
     */
    public function asFunction(string $functionName, array $params = []): void
    {
        $this->functionName = $functionName;
        $this->functionParams = $params;
    }

    /**
     * @param string $modal
     * @return Column
     * @throws Exception
     */
    public function modalable(string $modal): self
    {
        if ($this->confirmTitle)
        {
            throw new Exception('Column already confirmable', 'column_confirmable');
        }

        $this->modal = $modal;

        return $this;
    }

    /**
     * @param string $icon
     * @param string|null $color
     * @return Column
     */
    public function iconable(string $icon, string $color = null): self
    {
        $this->icon = $icon;
        $this->iconColor = $color;

        return $this;

    }

    /**
     * @param bool $value
     * @return void
     */
    public function isAjaxable(bool $value = true)
    {
        $this->ajaxable = $value;
    }

    /**
     * @param string $icon
     * @param string $colorTrue
     * @param string $colorFalse
     * @return Column
     */
    public function iconableBoolean(string $icon, string $colorTrue = 'green', string $colorFalse = 'red'): self
    {
        $this->icon = $icon;
        $this->iconColorBoolean = [
            'true' => $colorTrue,
            'false' => $colorFalse
        ];

        return $this;
    }

    /**
     * @param string $title
     * @param string $message
     * @return Column
     * @throws Exception
     */
    public function confirmable(string $title, string $message): self
    {
        if ($this->modal)
        {
            throw new Exception('Column already modalable', 'column_modalable');
        }

        $this->confirmTitle = $title;
        $this->confirmMessage = $message;


        return $this;
    }

    /**
     * @param int $width
     * @return Column
     */
    public function width(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return null
     */
    public function getAlign()
    {
        return  $this->align;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return  $this->width;
    }

    /**
     * @param object $item
     * @return string
     */
    public function getValue(object $item): ? string
    {
        switch ($this->type) {
            case "image":
                if (isset($item->mainImage->original_file_name)){
                    $image = $item->mainImage->original_file_name;
                } else {
                    $image = '/core/adminLTE/assets/app/media/img/error/noimage.png';
                }
                return '<img width="90"  src="' . $image . '">';
            default:
                if (!$this->icon ) {
                    return $this->extractModelValue($item);
                }

                if ($this->icon) {
                    $colorStyle = ($this->iconColor) ? 'style="color:'. $this->iconColor .'"' : '';
                    $icon = ($this->iconColorBoolean) ? '<i class="'. $this->icon .'" style="color:'. $this->iconColorBoolean[$this->extractBoleanModelValue($item)] .'"></i>' : '<i class="'. $this->icon .'" '. $colorStyle .'></i>';
                    if (!$this->route) {
                        return $icon;
                    } else {
                        if ($this->ajaxable) {
                            return '<a style="text-decoration:none" href="#" class="handle-click" '. $this->getLinkParams($item) . '>' . $icon . '</a>';
                        } else {
                            return '<a style="text-decoration:none"'. $this->getLinkParams($item) . '>' . $icon . '</a>';
                        }
                    }
                }
        }
        return '';
    }


    /**
     * @param $item
     * @return string
     */
    private function getLinkParams($item): string
    {

        $linkParams = [];
        $params = [];

        if ($this->routeParams)
        {
            foreach ($this->routeParams as $routeParam => $itemField)
            {
                $params[$routeParam] = $item->getOriginal($itemField);
            }

            $url = route($this->route, $params);

            if ($this->ajaxable) {
                $linkParams[] = 'data-url="' . $url . '"';
            } else {
                $linkParams[] = 'href="' . $url . '"';
            }
        }

        if ($this->modal)
        {
            $linkParams[] = 'data-type="modal"';
            $linkParams[] = 'data-modal="' . $this->modal . '"';
        } elseif ($this->confirmTitle) {
            $linkParams[] = 'data-type="confirm"';
            $linkParams[] = 'data-title="'. $this->confirmTitle .'"';
            $linkParams[] = 'data-message="'. $this->confirmMessage .'"';
        } else {
            if ($this->ajaxable) {
                $linkParams[] = 'data-type="ajax-get"';

            }
        }


        return implode(' ', $linkParams);
    }

    /**
     * @param object $model
     * @return mixed
     */
    private function extractModelValue(object $model)
    {

        return (!$this->functionName) ? $model->{$this->field} : call_user_func_array([$model, $this->functionName], $this->functionParams);
    }

    /**
     * @param $model
     * @return string
     */
    private function extractBoleanModelValue($model)
    {
        return ($model->getOriginal($this->field)) ? 'true' : 'false';
    }



}
