<?php

namespace Lodipay\DTO\Tests\DTO;

use Carbon\CarbonImmutable;
use Symfony\Component\Serializer\Annotation\Context;
use Lodipay\DTO\Attributes\MapTo;
use Lodipay\DTO\DTO\TseDTO;
use Lodipay\DTO\Serializer\Normalizer\CarbonNormalizer;
use Lodipay\DTO\Tests\Enum\Status;

class ChildDTO extends TseDTO
{
    public int $id;
    #[MapTo('name')]
    public string $title;

    #[Context(normalizationContext: [CarbonNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    public CarbonImmutable $createdAt;

    public Status $status = Status::PENDING;
}
