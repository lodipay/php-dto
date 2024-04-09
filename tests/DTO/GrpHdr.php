<?php

namespace Lodipay\DTO\Tests\DTO;

use Symfony\Component\Serializer\Attribute\SerializedPath;

class GrpHdr
{
    public string $MsgId;

    #[Context(
        normalizationContext: [
            DateTimeNormalizer::FORMAT_KEY => 'c',
        ],
        denormalizationContext: [
            DateTimeNormalizer::FORMAT_KEY => 'c',
        ],
    )]
    public \DateTimeImmutable $CreDtTm;
    public string $TxsCd;
    public int $NbOfTxs;
    public int $CtrlSum;
    #[SerializedPath('[InitgPty][Id][OrgId][AnyBIC]')]
    public string $InitgPtyIdOrgIdAnyBIC;
}
