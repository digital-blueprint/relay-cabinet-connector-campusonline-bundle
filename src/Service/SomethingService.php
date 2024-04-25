<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Service;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\Entity\Something;

class SomethingService
{
    public function setConfig(array $config): void
    {
    }

    public function getSomething(string $identifier, array $filters = [], array $options = []): ?Something
    {
        return null;
    }

    /**
     * @return Something[]
     */
    public function getSomethings(int $currentPageNumber, int $maxNumItemsPerPage, array $filters, array $options): array
    {
        return [];
    }

    public function addSomething(Something $data): Something
    {
        return $data;
    }

    public function removeSomething(Something $data)
    {
    }
}
