<?php namespace App\Mail;

/**
 * Trait CommonTrait
 * @package App\Mail
 */
trait CommonTrait {

    /**
     * @param string $field
     * @param string $locale
     * @param array $data
     * @return string
     */
    private function getField(string $field, string $locale, array $data): string
    {
       return (isset($data[$field][$locale])) ? $data[$field][$locale] : '';
    }

    /**
     * @param array $variables
     * @param string $data
     * @return string
     */
    private function parseVariables(array $variables, string $data): string
    {
        return str_replace(array_keys($variables), array_values($variables), $data);
    }
}
