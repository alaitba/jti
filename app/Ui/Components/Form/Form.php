<?php namespace App\Ui\Components\Form;

use App\Ui\Content;

/**
 * Class Form
 * @package App\Ui\Components\Form
 */
class Form extends Content
{
    /**
     * @var string|null
     */
    private $method = null;
    /**
     * @var string|null
     */
    private $action = null;
    /**
     * @var string
     */
    private $submitButtonText;
    /**
     * @var array
     */
    private $locales = [];
    /**
     * @var bool
     */
    private $ajaxable = false;
    /**
     * @var string|null
     */
    private $id = null;
    /**
     * @var null
     */
    private $dataModel = null;

    /**
     * Form constructor.
     * @param string $method
     * @param string $action
     * @param string $submitButtonText
     * @param string $id
     */
    public function __construct(string $method, string $action, string $submitButtonText, string $id)
    {
        $this->method = $method;
        $this->action = $action;
        $this->id = $id;
        $this->submitButtonText = $submitButtonText;
    }

    /**
     *
     */
    public function setAjaxable(): void
    {
        $this->ajaxable = true;
    }

    /**
     * @param array $locales
     */
    public function setLocaleable(array $locales): void
    {
        $this->locales = $locales;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return bool
     */
    public function isAjaxable(): bool
    {
        return $this->ajaxable;
    }

    /**
     * @return bool
     */
    public function hasData(): bool
    {
        return ($this->dataModel) ? true : false;
    }

    /**
     * @param string $field
     * @return string
     */
    public function getDataTextField(string $field): ?string
    {
        if ($this->hasData())
        {
            return $this->dataModel->{$field};
        }
        return null;
    }

    /**
     * @param string $field
     * @param $locale
     * @return string|null
     */
    public function getDataTextFieldWithLocale(string $field, $locale): ?string
    {
        if ($this->hasData())
        {
            return $this->dataModel->getTranslation($field, $locale);
        }
        return null;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isDataFieldTrue(string $field): bool
    {
        if ($this->hasData() && $this->dataModel->getOriginal($field))
        {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @return bool
     */
    public function isLocalable(): bool
    {
        return (count($this->locales)) ? true : false;
    }

    /**
     * @return array
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @return string
     */
    public function getSubmitButtonText(): string
    {
        return $this->submitButtonText;
    }

    /**
     * @param object $dataModel
     * @return $this
     */
    public function setDataModel(object $dataModel)
    {
        $this->dataModel = $dataModel;
        return $this;
    }

    /**
     * @return object
     */
    public function getDataModel(): object
    {
        return $this->dataModel;
    }

}
