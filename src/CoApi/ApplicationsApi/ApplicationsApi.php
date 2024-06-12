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

    /**
     * @return Application[]
     */
    public function getApplications(int $studentPersonNumber): array
    {
        $resources = $this->api->getResourceCollection(filters: ['StPersonNr' => (string) $studentPersonNumber]);
        $applications = [];
        foreach ($resources as $resource) {
            $applications[] = new Application($resource->data);
        }

        return $applications;
    }
}
