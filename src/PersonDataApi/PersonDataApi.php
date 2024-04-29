<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi;

use Dbp\CampusonlineApi\Rest\Api;
use Dbp\CampusonlineApi\Rest\Generic\GenericApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;

class PersonDataApi
{
    private ConfigurationService $config;

    private ?Api $api;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
        $this->api = null;
    }

    public function setClientHandler(?object $handler, string $token): void
    {
        $api = $this->getApi();
        $api->setClientHandler($handler);
        $api->getConnection()->setToken($token);
    }

    public function getGenericApi(): GenericApi
    {
        $config = $this->config;

        return $this->getApi()->Generic($config->getDataServiceNamePersonData());
    }

    public function getApi(): Api
    {
        if ($this->api === null) {
            $config = $this->config;
            $this->api = new Api($config->getApiUrl(), $config->getClientId(), $config->getClientSecret());
        }

        return $this->api;
    }

    public function getPersonData(string $obfuscatedId): ?PersonData
    {
        $api = $this->getGenericApi();
        $resource = $api->getResource('IDENTNROBFUSCATED', $obfuscatedId);
        if ($resource === null) {
            return null;
        }

        return new PersonData($resource->content);
    }
}
