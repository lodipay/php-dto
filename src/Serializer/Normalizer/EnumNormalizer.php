<?php

namespace Lodipay\DTO\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EnumNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return string|int
     *
     * @throws \InvalidArgumentException
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        if (!$object instanceof \BackedEnum) {
            throw new \InvalidArgumentException('The object must be Enum.');
        }

        return $object->value;
    }

    public function supportsNormalization($data, ?string $format = null)
    {
        return $data instanceof \BackedEnum;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return \BackedEnum
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if (!enum_exists($type)) {
            throw new NotNormalizableValueException(sprintf('%s is not enum', $type));
        }

        try {
            if (!method_exists($type, 'from')) {
                throw new \Exception('Enum must have method "from"');
            }

            return $type::from($data);
        } catch (\Exception $e) {
            throw new NotNormalizableValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return enum_exists($type);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === static::class;
    }
}
