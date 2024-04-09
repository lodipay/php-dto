<?php

namespace Lodipay\DTO\Serializer;

use Lodipay\DTO\Attributes\MapFrom;
use Lodipay\DTO\Attributes\MapTo;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;

class AttributeNameConverter implements AdvancedNameConverterInterface
{
    /**
     * @param ?class-string         $className
     * @param ?array<string, mixed> $context
     */
    public function normalize(
        string $propertyName,
        string $className = null,
        string $format = null,
        ?array $context = [],
    ): string {
        if (null !== $className) {
            $reflectionClass = new \ReflectionClass($className);

            if (!$reflectionClass->hasProperty($propertyName)) {
                return $propertyName;
            }

            $reflectionProperty = $reflectionClass->getProperty($propertyName);
            $attributes = $reflectionProperty->getAttributes(MapTo::class);

            foreach ($attributes as $attribute) {
                /** @var MapTo $mapTo */
                $mapTo = $attribute->newInstance();

                return $mapTo->name;
            }
        }

        return $propertyName;
    }

    /**
     * @param ?class-string         $className
     * @param ?array<string, mixed> $context
     */
    public function denormalize(
        string $propertyName,
        string $className = null,
        string $format = null,
        ?array $context = []
    ): string {
        if (null !== $className) {
            $reflectionClass = new \ReflectionClass($className);
            $properties = $reflectionClass->getProperties();

            foreach ($properties as $property) {
                $attributes = $property->getAttributes(MapFrom::class);

                foreach ($attributes as $attribute) {
                    /** @var MapFrom $mapFrom */
                    $mapFrom = $attribute->newInstance();

                    if ($mapFrom->name === $propertyName) {
                        return $property->getName();
                    }
                }
            }
        }

        return $propertyName;
    }
}
