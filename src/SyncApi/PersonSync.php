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
    private ConfigurationService $config;

    public function __construct(ConfigurationService $config)
    {
        $coApi = new CoApi($config);
        $this->syncApi = new SyncApi($coApi);
        $this->config = $config;
    }

    public function getPerson(string $id): ?array
    {
        return $this->syncApi->getSingleForObfuscatedId($id);
    }

    public function getAllPersons(?string $cursor = null): PersonSyncResultInterface
    {
        if ($cursor === null) {
            return $this->syncApi->getAll($this->config->getExcludeInactive());
        } else {
            return $this->syncApi->getAllSince($cursor);
        }
    }
}
