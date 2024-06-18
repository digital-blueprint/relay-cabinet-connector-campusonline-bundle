<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseResource;

class Utils
{
    /**
     * Converts the timestamp returned from the API to something we can use for comparison, i.e.
     * check for two values which one is newer.
     */
    public static function syncTimestampToIso(string $timestamp): string
    {
        $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y\TH:i:s', $timestamp);
        if ($dateTime === false) {
            throw new \RuntimeException('Invalid timestamp format');
        }

        return $dateTime->format('Y-m-d\TH:i:s');
    }

    /**
     * Like strcmp() but compares the sync timestamps.
     */
    public static function compareSyncTimestamps(string $timestamp1, string $timestamp2): int
    {
        $dateTime1 = self::syncTimestampToIso($timestamp1);
        $dateTime2 = self::syncTimestampToIso($timestamp2);
        if ($dateTime1 < $dateTime2) {
            return -1;
        } elseif ($dateTime1 > $dateTime2) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Allows finding the earliest timestamp of a set of resources by passing them all in on after
     * the other.
     */
    public static function updateMinSyncTimestamp(BaseResource $resource, ?string &$minTimestamp): void
    {
        $syncDate = $resource->getSyncTimestamp();

        if ($minTimestamp === null || self::compareSyncTimestamps($syncDate, $minTimestamp) < 0) {
            $minTimestamp = $syncDate;
        }
    }
}
