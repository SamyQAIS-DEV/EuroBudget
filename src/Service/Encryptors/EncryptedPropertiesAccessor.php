<?php

namespace App\Service\Encryptors;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class EncryptedPropertiesAccessor
{
    private const ENCRYPTED_ATTRIBUTE_NAME = 'App\Attribute\Encrypted';

    /**
     * @param Object $entity doctrine entity
     *
     * @return ReflectionProperty[]
     *
     * @throws ReflectionException
     */
    public function getProperties(object $entity): array
    {
        $properties = $this->getClassProperties(get_class($entity));
        $encryptedProperties = [];
        foreach ($properties as $refProperty) {
            if ($this->isPropertiesWithEncryptedAttribute($refProperty)) {
                $encryptedProperties[$refProperty->getName()] = $refProperty;
            }
        }
        return $encryptedProperties;
    }

    /**
     * Recursive function to get an associative array of class properties
     * including inherited ones from extended classes
     *
     * @param string $className Class name
     *
     * @return array<ReflectionProperty>
     * @throws ReflectionException
     */
    private function getClassProperties(string $className): array
    {
        if (class_exists($className)) {
            $reflectionClass = new ReflectionClass($className);
            $properties = $reflectionClass->getProperties();
            $propertiesArray = array();

            foreach ($properties as $property) {
                $propertyName = $property->getName();
                $propertiesArray[$propertyName] = $property;
            }
            $parentClass = $reflectionClass->getParentClass();
            if ($parentClass) {
                $parentPropertiesArray = $this->getClassProperties($parentClass->getName());
                if (count($parentPropertiesArray) > 0) {
                    $propertiesArray = array_merge($parentPropertiesArray, $propertiesArray);
                }
            }
            return $propertiesArray;
        }
        return [];
    }

    /**
     * @param ReflectionProperty $reflectionProperty
     * @return bool
     */
    private function isPropertiesWithEncryptedAttribute(ReflectionProperty $reflectionProperty): bool
    {
        foreach ($reflectionProperty->getAttributes() as $attribute) {
            if ($attribute->getName() === self::ENCRYPTED_ATTRIBUTE_NAME) {
                return true;
            }
        }
        return false;
    }
}
