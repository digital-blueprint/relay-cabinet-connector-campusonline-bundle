<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\SyncApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;

class StudiesApi
{
    private ConfigurationService $config;

    private ?SyncApi $api;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
        $this->api = null;
    }

    public function setClientHandler(?object $handler, string $token): void
    {
        $api = $this->getApi();
        $connection = $api->getConnection();
        $connection->setClientHandler($handler);
        $connection->setToken($token);
    }

    public function getApi(): SyncApi
    {
        if ($this->api === null) {
            $config = $this->config;

            $this->api = SyncApi::create($config->getApiUrl(), $config->getDataServiceNameStudies(), $config->getClientId(), $config->getClientSecret());
        }

        return $this->api;
    }

    /**
     * Returns 0 or more studies for a specific person.
     *
     * @return Study[]
     */
    public function getStudies(int $studentPersonNumber): array
    {
        $api = $this->getApi();
        $resources = $api->getResourceCollection(filters: ['StPersonNr' => (string) $studentPersonNumber]);
        $studies = [];
        foreach ($resources as $resource) {
            $studies[] = new Study($resource->content);
        }

        return $studies;
    }
}
