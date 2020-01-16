<?php namespace App\Ui\Components\Form;

/**
 * Class Input
 * @package App\Ui\Components\Form
 */
class Input extends Component
{
    /**
     * @var
     */
    protected $type;

    /**
     * @var bool
     */
    protected $tabeable = true;


    /**
     * @var bool
     */
    protected $disableable = false;

    /**
     * @var bool
     */
    protected $localeable = true;

    /**
     * Input constructor.
     * @param string $label
     * @param string $name
     * @param string $type
     * @param bool $tabeable
     * @param bool $localeable
     * @param bool $disableable
     */
    public function __construct(string $label, string $name, $type = "text", $tabeable = true, bool $localeable = true, $disableable = false)
    {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->tabeable = $tabeable;
        $this->localeable = $localeable;
        $this->disableable = $disableable;
    }

    /**
     * @return $this
     */
    public function datePicker()
    {
        $this->datePickerable = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function dateTimePicker()
    {
        $this->dateTimePickerable = true;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isDisableable(): bool
    {
        return $this->disableable;
    }


}
