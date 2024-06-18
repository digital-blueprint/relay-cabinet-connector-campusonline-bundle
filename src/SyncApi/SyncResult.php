<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

class SyncResult
{
    /**
     * @param array<string, array> $results
     */
    public function __construct(private array $results, private string $cursor)
    {
    }

    /**
     * @return array<string, array>
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * An opaque string which can be used to continue the sync.
     */
    public function getCursor(): string
    {
        return $this->cursor;
    }
}
