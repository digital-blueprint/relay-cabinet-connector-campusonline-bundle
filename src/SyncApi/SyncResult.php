<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

class SyncResult
{
    public function __construct(private array $results, private bool $complete, private string $cursor)
    {
    }

    /**
     * The added/changed items.
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Whether all entries have been synced at least once. Any future syncs using the same cursor
     * will only return entries that have changed.
     */
    public function isComplete(): bool
    {
        return $this->complete;
    }

    /**
     * An opaque string which can be used to continue the sync.
     */
    public function getCursor(): string
    {
        return $this->cursor;
    }
}
