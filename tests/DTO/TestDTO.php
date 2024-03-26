<?php

namespace Lodipay\DTO\Tests\DTO;

use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Lodipay\DTO\Attributes\MapFrom;
use Lodipay\DTO\Attributes\MapTo;
use Lodipay\DTO\DTO\TseDTO;
use Lodipay\DTO\Serializer\Normalizer\CarbonNormalizer;

class TestDTO extends TseDTO
{
    public int $age;
    public string $name;

    public ?string $firstName = null;

    #[MapFrom('register_id')]
    #[MapTo('register_number')]
    public string $registerNumber;

    #[Context(
        normalizationContext: [
            DateTimeNormalizer::FORMAT_KEY => 'c',
        ],
        denormalizationContext: [
            DateTimeNormalizer::FORMAT_KEY => 'm-d-Y H:i:s',
        ],
    )]
    public ?\DateTimeImmutable $date = null;

    #[Context(
        denormalizationContext: [
            CarbonNormalizer::FORMAT_KEY => 'X',
        ],
    )]
    public ?Carbon $dateFromTimestamp = null;
    public ?Carbon $dateNull = null;

    public ChildDTO $child;

    /**
     * @var array<ChildDTO>
     */
    public array $children = [];

    public function addChildren(ChildDTO $child): void
    {
        $this->children[] = $child;
    }
}
