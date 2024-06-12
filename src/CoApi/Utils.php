<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

class Utils
{
    /**
     * Converts the timestamp returned from the API to something we can use for comparison, i.e.
     * check for two values which one is newer.
     */
    public static function sourceTimestampToIso(string $timestamp): string
    {
        $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y\TH:i:s', $timestamp);
        if ($dateTime === false) {
            throw new \RuntimeException('Invalid timestamp format');
        }

        return $dateTime->format('Y-m-d\TH:i:s');
    }
}
