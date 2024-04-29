<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\ActiveStudiesApi;

use Dbp\CampusonlineApi\Rest\Api;
use Dbp\CampusonlineApi\Rest\FilterBuilder;
use Dbp\CampusonlineApi\Rest\Generic\GenericApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;

class ActiveStudiesApi
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

        return $this->getApi()->Generic($config->getDataServiceNameActiveStudies());
    }

    public function getApi(): Api
    {
        if ($this->api === null) {
            $config = $this->config;
            $this->api = new Api($config->getApiUrl(), $config->getClientId(), $config->getClientSecret());
        }

        return $this->api;
    }

    /**
     * Returns 0 or more studies for a specific person.
     *
     * @return Study[]
     */
    public function getActiveStudies(int $studentPersonNumber): array
    {
        $api = $this->getGenericApi();
        $filter = new FilterBuilder();
        $filter->eq('STPERSONNR', (string) $studentPersonNumber);
        $resources = $api->getResourceCollection($filter->getFilters());
        $studies = [];
        foreach ($resources as $resource) {
            $studies[] = new Study($resource->content);
        }

        return $studies;
    }
}
