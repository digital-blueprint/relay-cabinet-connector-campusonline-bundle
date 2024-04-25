<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Rest;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\Entity\Something;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\SomethingService;
use Dbp\Relay\CoreBundle\Rest\AbstractDataProcessor;

class SomethingProcessor extends AbstractDataProcessor
{
    private SomethingService $placeService;

    public function __construct(SomethingService $placeService)
    {
        $this->placeService = $placeService;
    }

    protected function addItem($data, array $filters)
    {
        assert($data instanceof Something);

        $data->setIdentifier('42');

        return $this->placeService->addSomething($data);
    }

    protected function removeItem($identifier, $data, array $filters): void
    {
        assert($data instanceof Something);

        $this->placeService->removeSomething($data);
    }

    protected function isUserGrantedOperationAccess(int $operation): bool
    {
        return $this->isAuthenticated();
    }
}
