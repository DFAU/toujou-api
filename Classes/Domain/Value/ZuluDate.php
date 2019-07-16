<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Value;


class ZuluDate
{

    const FORMAT = 'Y-m-d\TH:i:s\Z';

    static public function fromTimestamp(int $timestamp) : string
    {
        return (new \DateTime('@' . $timestamp, new \DateTimeZone('UTC')))->format(static::FORMAT);
    }
}
