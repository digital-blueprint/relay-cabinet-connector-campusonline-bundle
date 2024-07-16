<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

class Utils
{
    /**
     * Converts a sync timestamp to a DateTimeInterface with a UTC timezone. $timezone needs to be the timezone
     * of the sync timestamp.
     */
    public static function syncTimestampToDateTimeUTC(string $timestamp, \DateTimeZone $timezone): \DateTimeInterface
    {
        $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y\TH:i:s', $timestamp, $timezone);
        if ($dateTime === false) {
            throw new \RuntimeException('Invalid timestamp format');
        }

        return $dateTime->setTimezone(new \DateTimeZone('UTC'));
    }
}
