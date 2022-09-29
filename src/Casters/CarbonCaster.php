<?php

namespace Tsetsee\DTO\Casters;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeZone;
use Spatie\DataTransferObject\Caster;

class CarbonCaster implements Caster
{
    /**
     * @param array<mixed>             $types
     * @param string|DateTimeZone|null $tz
     */
    public function __construct(
        private array $types,
        public string $type = 'default',
        public string $format = 'Y-m-d H:i:s',
        public $tz = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function cast(mixed $value): mixed
    {
        if (in_array(CarbonImmutable::class, $this->types)) {
            $class = CarbonImmutable::class;
        } else {
            $class = Carbon::class;
        }

        if ('timestamp' === $this->type) {
            return $class::createFromTimestamp(intval($value));
        }

        return $class::createFromFormat($this->format, strval($value));
    }
}