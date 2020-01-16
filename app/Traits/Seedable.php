<?php

namespace App\Traits;

/**
 * Trait Seedable
 * @package App\Traits
 */
trait Seedable
{
    /**
     * @param $class
     */
    public function seed($class)
    {
        if (!class_exists($class)) {
            require_once $this->seedersPath.$class.'.php';
        }

        with(new $class())->run();
    }
}
