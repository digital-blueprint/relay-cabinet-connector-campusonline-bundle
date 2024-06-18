<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

class SyncApi
{
    public function __construct()
    {
    }

    public function sync(?string $cursor = null): SyncResult
    {
        // TODO
        return new SyncResult([], true, '');
    }

    public function syncUtilComplete(): SyncResult
    {
        $results = [];
        $cursor = null;
        $complete = false;
        while (!$complete) {
            $res = $this->sync($cursor);
            $results = array_merge($results, $res->getResults());
            $cursor = $res->getCursor();
            $complete = $res->isComplete();
        }

        return new SyncResult($results, $complete, $cursor);
    }
}
