<?php

namespace Lodipay\DTO\Attributes;

#[\Attribute()]
class MapFrom
{
    public function __construct(public string $name)
    {
    }
}
