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

    public function getStudentForPersonNumber(int $studentPersonNumber): ?Student
    {
        $resource = $this->api->getResource('StPersonNr', (string) $studentPersonNumber);
        if ($resource === null) {
            return null;
        }

        return new Student($resource->data);
    }

    /**
     * @return iterable<Student>
     */
    public function getActiveStudents(?string $lastSyncDate = null): iterable
    {
        $resources = $this->api->getResourceCollection($lastSyncDate);
        foreach ($resources as $resource) {
            $student = new Student($resource->data);
            yield $student;
        }
    }

    /**
     * @return iterable<Student>
     */
    public function getInactiveStudents(int $pageSize): iterable
    {
        $page = 1;
        while (1) {
            $resources = $this->api->getResourceCollection(syncOnlyInactive: true, page: $page, pageSize: $pageSize);
            if ($resources === []) {
                break;
            }
            foreach ($resources as $resource) {
                $student = new Student($resource->data);
                yield $student;
            }
            ++$page;
        }
    }
}
