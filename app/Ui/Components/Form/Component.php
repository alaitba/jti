<?php namespace App\Ui\Components\Form;

/**
 * Class Component
 * @package App\Ui\Components\Form
 */
abstract class Component
{
    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $title;
    /**
     * @var
     */
    protected $label;

    protected $placeHolder;

    /**
     * @var bool
     */
    protected $localeable = true;

    protected $disabeable = false;

    protected $dateTimePickerable = false;


    protected $datePickerable = false;

    /**
     * @var bool
     */
    protected $tabeable = true;

    /**
     * @var bool
     */
    protected $editorable = false;

    protected $hasValue = true;

    protected $textareaRowsNumber = 3;

    /**
     * @return int
     */
    public function getTextareRowsNumber()
    {
        return $this->textareaRowsNumber;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isLocaleable(): bool
    {
        return $this->localeable;
    }

    /**
     * @return bool
     */
    public function isEditorable(): bool
    {
        return $this->editorable;
    }

    /**
     * @return bool
     */
    public function isTabeable(): bool
    {
        return $this->tabeable;
    }

    /**
     * @return bool
     */
    public function isDatePicker(): bool
    {
        return $this->datePickerable;
    }

    /**
     * @return bool
     */
    public function isDateTimePicker(): bool
    {
        return $this->dateTimePickerable;
    }

    /**
     * @return $this
     */
    public function hasNotValue(): self
    {
        $this->hasValue = false;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeHolder = $placeholder;
        return $this;
    }

    public function getPlaceholder()
    {
        return $this->placeHolder;
    }

    /**
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->hasValue;
    }

}
