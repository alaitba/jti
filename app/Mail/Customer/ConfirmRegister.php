<?php

namespace App\Mail\Customer;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use stdClass;

use Illuminate\Support\Facades\Log;

// models
use App\Contracts\NotifyTemplate as NotifyTemplateContract;

// traits
use App\Mail\CommonTrait;

/**
 * Class ConfirmRegister
 * @package App\Mail\Customer
 */
class ConfirmRegister extends Mailable
{
    use Queueable, SerializesModels;
    use CommonTrait;

    protected $params;


    /**
     * Create a new message instance.
     *
     * @param stdClass $params
     */
    public function __construct(stdClass $params)
    {
        $this->params = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $templateName = 'CustomerRegistrationConfirmMail';

        $template = app(NotifyTemplateContract::class)->where('name', $templateName)->first();

        if (!$template)
        {
            Log::error('Email template: "' . $templateName . '" not found');
            exit;
        }

        $data = json_decode($template->data, true);

        if (!is_array($data))
        {
            Log::error('Template:"' . $templateName . '". Data not array');
            exit;
        }



        $subject = $this->getField('subject',$this->params->locale,  $data);
        $contentRaw = $this->getField('body', $this->params->locale, $data);
        $content = $this->parseVariables($this->params->variables, $contentRaw);

        return $this->subject($subject)->markdown('mail.customer.auth.registration_confirmation', [
            'content' => $content
        ]);
    }
}
