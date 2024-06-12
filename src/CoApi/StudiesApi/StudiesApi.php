<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

use Dbp\CampusonlineApi\Rest\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;

class StudiesApi
{
    private BaseApi $api;

    public function __construct(Connection $connection, string $dataServiceName)
    {
        $this->api = new BaseApi($connection, $dataServiceName);
    }

    /**
     * Returns 0 or more studies for a specific person.
     *
     * @return Study[]
     */
    public function getStudies(int $studentPersonNumber): array
    {
        $resources = $this->api->getResourceCollection(filters: ['StPersonNr' => (string) $studentPersonNumber]);
        $studies = [];
        foreach ($resources as $resource) {
            $studies[] = new Study($resource->data);
        }

        return $studies;
    }
}
