<?php

namespace Lodipay\DTO\Serializer\Normalizer;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonTimeZone;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CarbonNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    public const FORMAT_KEY = 'datetime_format';
    public const TIMEZONE_KEY = 'datetime_timezone';

    /** @var array<string, mixed> */
    private $defaultContext = [
        self::FORMAT_KEY => CarbonInterface::RFC3339,
        self::TIMEZONE_KEY => null,
    ];

    private const SUPPORTED_TYPES = [
        CarbonInterface::class => true,
        CarbonImmutable::class => true,
        Carbon::class => true,
    ];

    /**
     * @param array<string, mixed> $defaultContext
     */
    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return string|int
     *
     * @throws \InvalidArgumentException
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        if (!$object instanceof CarbonInterface) {
            throw new \InvalidArgumentException('The object must implement the "CarbonInterface".');
        }

        /** @var string $dateTimeFormat */
        $dateTimeFormat = $context[self::FORMAT_KEY] ?? $this->defaultContext[self::FORMAT_KEY];

        $timezone = $this->getTimezone($context);

        if (null !== $timezone) {
            $object = clone $object;
            $object = $object->setTimezone($timezone);
        }

        switch ($dateTimeFormat) {
            case 'x':
                return $object->getTimestampMs();
            case 'X':
                return $object->getTimestamp();
        }

        return $object->format($dateTimeFormat);
    }

    public function supportsNormalization($data, ?string $format = null)
    {
        return $data instanceof CarbonInterface;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return CarbonInterface
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $dateTimeFormat = $context[self::FORMAT_KEY] ?? null;

        if (!is_string($dateTimeFormat)) {
            $dateTimeFormat = null;
        }

        $timezone = $this->getTimezone($context);

        if (is_int($data)) {
            $data = strval($data);
        }

        if (null === $data || !is_string($data) || '' === trim($data)) {
            throw new NotNormalizableValueException('The data is either an empty string or null, you should pass a string that can be parsed with the passed format or a valid DateTime string.');
        }

        if (null !== $dateTimeFormat) {
            switch ($dateTimeFormat) {
                case 'X':
                    $object = Carbon::class === $type ? Carbon::createFromTimestamp($data, $timezone) : CarbonImmutable::createFromTimestamp($data, $timezone);
                    break;
                case 'x':
                    $object = Carbon::class === $type ? Carbon::createFromTimestampMs($data, $timezone) : CarbonImmutable::createFromTimestampMs($data, $timezone);
                    break;
                default:
                    $object = Carbon::class === $type ? Carbon::createFromFormat($dateTimeFormat, $data, $timezone) : CarbonImmutable::createFromFormat($dateTimeFormat, $data, $timezone);
            }

            if (false !== $object) {
                return $object;
            }

            $dateTimeErrors = Carbon::class === $type ? Carbon::getLastErrors() : CarbonImmutable::getLastErrors();

            throw new NotNormalizableValueException(sprintf('Parsing datetime string "%s" using format "%s" resulted in %d errors: ', $data, $dateTimeFormat, $dateTimeErrors['error_count'])."\n".implode("\n", $this->formatDateTimeErrors($dateTimeErrors['errors'])));
        }

        try {
            return Carbon::class === $type ? new Carbon($data, $timezone) : new CarbonImmutable($data, $timezone);
        } catch (\Exception $e) {
            throw new NotNormalizableValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return isset(self::SUPPORTED_TYPES[$type]);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === static::class;
    }

    /**
     * Formats datetime errors.
     *
     * @param array<string> $errors
     *
     * @return string[]
     */
    private function formatDateTimeErrors(array $errors): array
    {
        $formattedErrors = [];

        foreach ($errors as $pos => $message) {
            $formattedErrors[] = sprintf('at position %d: %s', $pos, $message);
        }

        return $formattedErrors;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function getTimezone(array $context): ?CarbonTimeZone
    {
        $dateTimeZone = $context[self::TIMEZONE_KEY] ?? $this->defaultContext[self::TIMEZONE_KEY];

        if (null === $dateTimeZone) {
            return null;
        }

        return $dateTimeZone instanceof CarbonTimeZone ? $dateTimeZone : new CarbonTimeZone($dateTimeZone);
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    public function getSupportedTypes(?string $format): array
    {
        return self::SUPPORTED_TYPES;
    }
}
