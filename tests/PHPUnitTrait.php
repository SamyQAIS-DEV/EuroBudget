<?php

namespace App\Tests;

use ReflectionClass;
use ReflectionException;

trait PHPUnitTrait
{
    /**
     * @throws ReflectionException
     */
    public function callMethod(object $obj, $name, array $args = []): mixed
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}