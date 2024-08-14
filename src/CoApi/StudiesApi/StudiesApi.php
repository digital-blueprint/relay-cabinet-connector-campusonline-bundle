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
            $studies[] = new Study($resource->data, $this->api);
        }

        return $studies;
    }

    /**
     * Returns all currently active studies.
     *
     * @return array<int, Study[]> a mapping of studentPersonNumber to Study
     */
    public function getActiveStudies(): array
    {
        $resources = $this->api->getResourceCollection();
        $studies = [];
        foreach ($resources as $resource) {
            $study = new Study($resource->data, $this->api);
            $studies[$study->getStudentPersonNumber()][] = $study;
        }

        return $studies;
    }

    /**
     * Returns all changed studies from the active pool since the $lastSyncDate.
     * In case a study enters the active pool, or leaves the active pool into the inactive one
     * they are also included. This means this can also return inactive studies in rare cases.
     * The sync timestamp of the returned records can be a bit older than $lastSyncDate.
     * A null $lastSyncDate results in all active studies being returned.
     *
     * @return array<int, Study[]> a mapping of studentPersonNumber to Study
     */
    public function getChangedStudiesSince(?\DateTimeInterface $lastSyncDate): array
    {
        $resources = $this->api->getResourceCollection(lastSyncDate: $lastSyncDate);
        $studies = [];
        foreach ($resources as $resource) {
            $study = new Study($resource->data, $this->api);
            $studies[$study->getStudentPersonNumber()][] = $study;
        }

        return $studies;
    }

    /**
     * @return array<int, Study[]> a mapping of studentPersonNumber to studies
     */
    public function getInactiveStudies(int $pageSize): array
    {
        $studies = [];

        $page = 1;
        while (1) {
            $resources = $this->api->getResourceCollection(syncOnlyInactive: true, page: $page, pageSize: $pageSize);
            foreach ($resources as $resource) {
                $study = new Study($resource->data, $this->api);
                $studies[$study->getStudentPersonNumber()][] = $study;
            }
            if (count($resources) < $pageSize) {
                break;
            }
            ++$page;
        }

        return $studies;
    }
}
