<?php

namespace Lodipay\DTO\DTO;

use Lodipay\DTO\Serializer\AttributeNameConverter;
use Lodipay\DTO\Serializer\Normalizer\CarbonNormalizer;
use Lodipay\DTO\Serializer\Normalizer\EnumNormalizer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class TseDTO
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<string,mixed>|array<int,mixed>|string|bool|int|float|\ArrayObject<string,mixed>|\ArrayObject<int,mixed>|null
     */
    public function toArray(?string $format = null, array $context = [])
    {
        return static::getSerializer()->normalize($this, $format, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function serialize(string $format = 'json', array $context = []): string
    {
        return static::getSerializer()->serialize($this, $format, $context);
    }

    /**
     * @param string|mixed  $payload
     * @param string        $source
     * @param array<string> $groups
     */
    public static function from(
        $payload,
        $source = 'array',
        array $groups = ['*'],
    ): static {
        $serializer = self::getSerializer();
        if ('array' === $source) {
            /** @var static $object */
            $object = $serializer->denormalize($payload, static::class, null, [
                'groups' => $groups,
            ]);
        } else {
            /** @var static $object */
            $object = $serializer->deserialize($payload, static::class, $source, [
                'groups' => $groups,
            ]);
        }

        return $object;
    }

    /**
     * @param array<mixed>  $data
     * @param array<string> $groups
     *
     * @return array<TseDTO>
     */
    public static function fromArray(array $data, array $groups = ['*']): array
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = self::from($item, 'array', $groups);
        }

        return $result;
    }

    protected static function getSerializer(): Serializer
    {
        $encoders = [new JsonEncoder(), new XmlEncoder()];
        $normalizers = [
            new CarbonNormalizer(),
            new DateTimeNormalizer(),
            new EnumNormalizer(),
            new ArrayDenormalizer(),
            static::getObjectNormalizer(),
        ];

        return new Serializer($normalizers, $encoders);
    }

    protected static function getObjectNormalizer(): ObjectNormalizer
    {
        $loader = new AnnotationLoader();
        $classMetadataFactory = new ClassMetadataFactory($loader);

        $nameConverter = new AttributeNameConverter();

        return new ObjectNormalizer($classMetadataFactory, $nameConverter, null, new ReflectionExtractor());
        // return new ObjectNormalizer(null, $nameConverter, null, new ReflectionExtractor());
    }
}
