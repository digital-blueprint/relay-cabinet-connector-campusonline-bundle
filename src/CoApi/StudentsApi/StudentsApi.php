<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;

class StudentsApi
{
    private BaseApi $api;

    public function __construct(...$args)
    {
        $this->api = new BaseApi(...$args);
    }

    public function getStudentForObfuscatedId(string $obfuscatedId): ?Student
    {
        $resource = $this->api->getResource('IdentNrObfuscated', $obfuscatedId);
        if ($resource === null) {
            return null;
        }

        return new Student($resource->data, $resource->syncTimeZone);
    }

    public function checkConnection(): void
    {
        $this->api->getResource('StPersonNr', '0');
    }

    public function getStudentForPersonNumber(int $studentPersonNumber): ?Student
    {
        $resource = $this->api->getResource('StPersonNr', (string) $studentPersonNumber);
        if ($resource === null) {
            return null;
        }

        return new Student($resource->data, $resource->syncTimeZone);
    }

    /**
     * @return iterable<Student>
     */
    public function getActiveStudents(?string $lastSyncDate = null): iterable
    {
        $resources = $this->api->getResourceCollection($lastSyncDate);
        foreach ($resources as $resource) {
            $student = new Student($resource->data, $resource->syncTimeZone);
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
                $student = new Student($resource->data, $resource->syncTimeZone);
                yield $student;
            }
            ++$page;
        }
    }
}
