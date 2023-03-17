<?php

namespace App\Service\Encryptors;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Throwable;

class EntityEncryptor
{
    public function __construct(
        private readonly EncryptorInterface $encryptor,
        private readonly EncryptedPropertiesAccessor $encryptedPropertiesAccessor,
        private readonly PropertyAccessorInterface $propertyAccessor
    ) {
    }

    /**
     * @param string[] $array
     * @param string $className
     * @return string[]
     */
    public function encryptAllEncryptedAttributeInArray(array $array, string $className): array
    {
        try {
            $encryptedProperties = $this->encryptedPropertiesAccessor->getProperties(new $className());
            $values = [];
            foreach ($array as $field => $value) {
                if (array_key_exists($field, $encryptedProperties) && $value) {
                    $value = $this->encryptor->encrypt($value);
                }
                $values[$field] = $value;
            }
            return $values;
        } catch (Throwable $exception) {
            return $array;
        }
    }

    /**
     * @param object $entity
     * @return void
     */
    public function decryptAllEncryptedAttribute(object $entity): void
    {
        try {
            foreach ($this->encryptedPropertiesAccessor->getProperties($entity) as $property) {
                $value = $this->propertyAccessor->getValue($entity, $property->getName());
                if (is_string($value)) {
                    $this->propertyAccessor->setValue($entity, $property->getName(), $this->decryptValue($value));
                } elseif (is_array($value)) {
                    $this->propertyAccessor->setValue($entity, $property->getName(), $this->decryptValues($value));
                }
            }
        } catch (Throwable $exception) {
            return;
        }
    }

    /**
     * @param object $entity
     * @return void
     */
    public function encryptAllEncryptedAttribute(object $entity): void
    {
        try {
            foreach ($this->encryptedPropertiesAccessor->getProperties($entity) as $property) {
                $value = $this->propertyAccessor->getValue($entity, $property->getName());
                if (is_string($value)) {
                    $this->propertyAccessor->setValue($entity, $property->getName(), $this->encryptValue($value));
                } elseif (is_array($value)) {
                    $this->propertyAccessor->setValue($entity, $property->getName(), $this->encryptValues($value));
                }
            }
        } catch (Throwable $exception) {
            return;
        }
    }

    private function decryptValue(string $value): ?string
    {
        if (str_ends_with($value, $this->encryptor->getSuffix())) {
            return $this->encryptor->decrypt(substr($value, 0, -strlen($this->encryptor->getSuffix())));
        }
        return $value;
    }

    /**
     * @param mixed[] $values
     * @return mixed[]
     */
    private function decryptValues(array $values): array
    {
        $decryptedValues = [];
        foreach ($values as $key => $value) {
            if (is_string($value)) {
                $decryptedValues[$key] = $this->decryptValue($value);
            } elseif (is_array($value)) {
                $decryptedValues[$key] = $this->decryptValues($value);
            }
        }
        return $decryptedValues;
    }

    /**
     * @param string $value
     * @return string
     */
    private function encryptValue(string $value): string
    {
        if (!str_ends_with($value, $this->encryptor->getSuffix())) {
            return $this->encryptor->encrypt($value);
        }
        return $value;
    }

    /**
     * @param mixed[] $values
     * @return mixed[]
     */
    private function encryptValues(array $values): array
    {
        $encryptedValues = [];
        foreach ($values as $key => $value) {
            if (is_string($value)) {
                $encryptedValues[$key] = $this->encryptValue($value);
            } elseif (is_array($value)) {
                $encryptedValues[$key] = $this->encryptValues($value);
            }
        }
        return $encryptedValues;
    }
}
