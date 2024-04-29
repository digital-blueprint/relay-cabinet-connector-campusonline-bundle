<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Service;

class SomethingService
{
    public function setConfig(array $config): void
    {
    }

    public function getSomething(string $identifier, array $filters = [], array $options = []): ?object
    {
        return null;
    }

    /**
     * @return object[]
     */
    public function getSomethings(int $currentPageNumber, int $maxNumItemsPerPage, array $filters, array $options): array
    {
        return [];
    }

    public function addSomething(object $data): object
    {
        return $data;
    }

    public function removeSomething(object $data)
    {
    }
}
