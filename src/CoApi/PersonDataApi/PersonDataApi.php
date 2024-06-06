<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\PersonDataApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\SyncApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;

class PersonDataApi
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

            $this->api = SyncApi::create($config->getApiUrl(), $config->getDataServiceNamePersonData(), $config->getClientId(), $config->getClientSecret());
        }

        return $this->api;
    }

    public function getPersonData(string $obfuscatedId): ?PersonData
    {
        $api = $this->getApi();
        $resource = $api->getResource('IdentNrObfuscated', $obfuscatedId);
        if ($resource === null) {
            return null;
        }

        return new PersonData($resource->content);
    }
}
