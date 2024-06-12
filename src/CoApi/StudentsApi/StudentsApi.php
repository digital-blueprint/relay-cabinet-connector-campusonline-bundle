<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi;

use Dbp\CampusonlineApi\Rest\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;

class StudentsApi
{
    private BaseApi $api;

    public function __construct(Connection $connection, string $dataServiceName)
    {
        $this->api = new BaseApi($connection, $dataServiceName);
    }

    public function getStudentForObfuscatedId(string $obfuscatedId): ?Student
    {
        $resource = $this->api->getResource('IdentNrObfuscated', $obfuscatedId);
        if ($resource === null) {
            return null;
        }

        return new Student($resource->data);
    }

    public function getStudentForPersonNumber(int $stPersonNr): ?Student
    {
        $resource = $this->api->getResource('StPersonNr', (string) $stPersonNr);
        if ($resource === null) {
            return null;
        }

        return new Student($resource->data);
    }
}
