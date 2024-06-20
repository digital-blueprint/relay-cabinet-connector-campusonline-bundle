<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi;

use Dbp\CampusonlineApi\Rest\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;

class ApplicationsApi
{
    private BaseApi $api;

    public function __construct(Connection $connection, string $dataServiceName)
    {
        $this->api = new BaseApi($connection, $dataServiceName);
    }

    public function checkConnection(): void
    {
        $this->api->getResource('StPersonNr', '0');
    }

    /**
     * @return Application[]
     */
    public function getApplicationsForPersonNumber(int $studentPersonNumber): array
    {
        $resources = $this->api->getResourceCollection(filters: ['StPersonNr' => (string) $studentPersonNumber]);
        $applications = [];
        foreach ($resources as $resource) {
            $applications[] = new Application($resource->data);
        }

        return $applications;
    }

    /**
     * @return array<int, Application[]>
     */
    public function getAllApplications(?string $lastSyncDate = null): array
    {
        $resources = $this->api->getResourceCollection($lastSyncDate);
        $applications = [];
        foreach ($resources as $resource) {
            $application = new Application($resource->data);
            $applications[$application->getStudentPersonNumber()][] = $application;
        }

        return $applications;
    }
}
