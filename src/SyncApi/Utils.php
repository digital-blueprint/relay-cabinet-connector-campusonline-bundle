<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseResource;

class Utils
{
    /**
     * Allows finding the earliest timestamp of a set of resources by passing them all in on after
     * the other.
     */
    public static function updateMinSyncDateTime(BaseResource $resource, ?\DateTimeInterface &$minDateTime): void
    {
        $syncDateTime = $resource->getSyncTimestamp();

        if ($minDateTime === null || $syncDateTime < $minDateTime) {
            $minDateTime = $syncDateTime;
        }
    }
}
