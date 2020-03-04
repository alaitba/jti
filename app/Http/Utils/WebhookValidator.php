<?php


namespace App\Http\Utils;


use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\WebhookFailed;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

/**
 * Class WebhookValidator
 * @package App\Http\Utils
 */
class WebhookValidator implements SignatureValidator
{
    /**
     * @param Request $request
     * @param WebhookConfig $config
     * @return bool
     * @throws WebhookFailed
     */
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $request->header($config->signatureHeaderName);

        if (!$signature) {
            return false;
        }

        $signingSecret = $config->signingSecret;

        if (empty($signingSecret)) {
            throw WebhookFailed::signingSecretNotSet();
        }

        return $signature == $signingSecret;
    }
}
