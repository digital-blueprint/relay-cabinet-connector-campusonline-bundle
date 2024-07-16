<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;

class StudiesApi
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
     * Returns 0 or more studies for a specific person.
     *
     * @return Study[]
     */
    public function getStudiesForPersonNumber(int $studentPersonNumber): array
    {
        $resources = $this->api->getResourceCollection(filters: ['StPersonNr' => (string) $studentPersonNumber]);
        $studies = [];
        foreach ($resources as $resource) {
            $studies[] = new Study($resource->data, $resource->syncTimeZone);
        }

        return $studies;
    }

    /**
     * @return array<int, Study[]>
     */
    public function getActiveStudies(?string $lastSyncDate = null): array
    {
        $resources = $this->api->getResourceCollection($lastSyncDate);
        $studies = [];
        foreach ($resources as $resource) {
            $study = new Study($resource->data, $resource->syncTimeZone);
            $studies[$study->getStudentPersonNumber()][] = $study;
        }

        return $studies;
    }

    /**
     * @return array<int, Study[]>
     */
    public function getInactiveStudies(int $pageSize): array
    {
        $studies = [];

        $page = 1;
        while (1) {
            $resources = $this->api->getResourceCollection(syncOnlyInactive: true, page: $page, pageSize: $pageSize);
            if ($resources === []) {
                break;
            }
            foreach ($resources as $resource) {
                $study = new Study($resource->data, $resource->syncTimeZone);
                $studies[$study->getStudentPersonNumber()][] = $study;
            }
            ++$page;
        }

        return $studies;
    }
}
