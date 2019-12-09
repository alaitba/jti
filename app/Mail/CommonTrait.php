<?php namespace App\Mail;

trait CommonTrait {

    private function getField(string $field, string $locale, array $data): string
    {
       return (isset($data[$field][$locale])) ? $data[$field][$locale] : '';
    }

    private function parseVariables(array $variables, string $data): string
    {
        return str_replace(array_keys($variables), array_values($variables), $data);
    }
}
