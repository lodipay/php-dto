<?php

namespace Lodipay\DTO\Attributes;

#[\Attribute()]
class MapTo
{
    public function __construct(public string $name)
    {
    }
}
