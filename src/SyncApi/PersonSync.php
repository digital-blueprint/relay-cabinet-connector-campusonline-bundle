<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetBundle\PersonSync\PersonSyncInterface;
use Dbp\Relay\CabinetBundle\PersonSync\PersonSyncResultInterface;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;

class PersonSync implements PersonSyncInterface
{
    private SyncApi $syncApi;

    public function __construct(ConfigurationService $config)
    {
        $coApi = new CoApi($config);
        $this->syncApi = new SyncApi($coApi);
    }

    public function getPerson(string $id): ?array
    {
        return $this->syncApi->getSingleForObfuscatedId($id);
    }

    public function getAllPersons(?string $cursor = null): PersonSyncResultInterface
    {
        if ($cursor === null) {
            return $this->syncApi->getAll();
        } else {
            return $this->syncApi->getAllSince($cursor);
        }
    }
}
