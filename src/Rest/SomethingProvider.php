<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Rest;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\Entity\Something;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\SomethingService;
use Dbp\Relay\CoreBundle\Rest\AbstractDataProvider;

/**
 * @extends AbstractDataProvider<Something>
 */
class SomethingProvider extends AbstractDataProvider
{
    private SomethingService $placeService;

    public function __construct(SomethingService $placeService)
    {
        $this->placeService = $placeService;
    }

    protected function getItemById(string $id, array $filters = [], array $options = []): ?object
    {
        return $this->placeService->getSomething($id, $filters, $options);
    }

    protected function getPage(int $currentPageNumber, int $maxNumItemsPerPage, array $filters = [], array $options = []): array
    {
        return $this->placeService->getSomethings($currentPageNumber, $maxNumItemsPerPage, $filters, $options);
    }

    protected function isUserGrantedOperationAccess(int $operation): bool
    {
        return $this->isAuthenticated();
    }
}
