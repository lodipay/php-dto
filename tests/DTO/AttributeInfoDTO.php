<?php

namespace Lodipay\DTO\Tests\DTO;

use Lodipay\DTO\DTO\TseDTO;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Lodipay\DTO\DTO\TseDTO;

class AttributeInfoDTO extends TseDTO
{
    #[SerializedName('@school')]
    public ?string $school;

    #[SerializedName('#')]
    public ?string $hobby;
}
