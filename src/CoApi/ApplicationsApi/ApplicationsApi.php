<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;

class ApplicationsApi
{
    private BaseApi $api;

    public function __construct(...$args)
    {
        $this->api = new BaseApi(...$args);
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
            $applications[] = new Application($resource->data, $this->api);
        }

        return $applications;
    }

    /**
     * Returns all changed applications since the $lastSyncDate.
     * The sync timestamp of the returned records can be a bit older than $lastSyncDate.
     *A null $lastSyncDate results in all active applications being returned.
     *
     * @return array<int, Application[]> a mapping of studentPersonNumber to applications
     */
    public function getChangedApplicationsSince(?\DateTimeInterface $lastSyncDate): array
    {
        $resources = $this->api->getResourceCollection(lastSyncDate: $lastSyncDate);
        $applications = [];
        foreach ($resources as $resource) {
            $application = new Application($resource->data, $this->api);
            $applications[$application->getStudentPersonNumber()][] = $application;
        }

        return $applications;
    }

    /**
     * Returns all applications.
     *
     * @return array<int, Application[]> a mapping of studentPersonNumber to applications
     */
    public function getAllApplications(): array
    {
        $resources = $this->api->getResourceCollection();
        $applications = [];
        foreach ($resources as $resource) {
            $application = new Application($resource->data, $this->api);
            $applications[$application->getStudentPersonNumber()][] = $application;
        }

        return $applications;
    }
}
