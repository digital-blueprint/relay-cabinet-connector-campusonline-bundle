<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetBundle\PersonSync\PersonSyncResultInterface;

readonly class SyncResult implements PersonSyncResultInterface
{
    /**
     * @param array[] $persons
     */
    public function __construct(private array $persons, private string $cursor, private bool $isFullSyncResult)
    {
    }

    /**
     * @return array[]
     */
    public function getPersons(): array
    {
        return $this->persons;
    }

    /**
     * An opaque string which can be used to continue the sync.
     */
    public function getCursor(): string
    {
        return $this->cursor;
    }

    public function isFullSyncResult(): bool
    {
        return $this->isFullSyncResult;
    }
}
