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
     * Returns all currently active students.
     *
     * @return iterable<Student>
     */
    public function getActiveStudents(): iterable
    {
        $resources = $this->api->getResourceCollection();
        foreach ($resources as $resource) {
            $student = new Student($resource->data, $resource->syncTimeZone);
            yield $student;
        }
    }

    /**
     * Returns all changed students from the active pool since the $lastSyncDate.
     * In case a student enters the active pool, or leaves the active pool into the inactive one
     * they are also included. This means this can also return inactive students in rare cases.
     * The sync timestamp of the returned records can be a bit older than $lastSyncDate.
     * * A null $lastSyncDate results in all active studies being returned.
     *
     * @return iterable<Student>
     */
    public function getChangedStudentsSince(?string $lastSyncDate): iterable
    {
        $resources = $this->api->getResourceCollection(lastSyncDate: $lastSyncDate);
        foreach ($resources as $resource) {
            $student = new Student($resource->data, $resource->syncTimeZone);
            yield $student;
        }
    }

    /**
     * Returns all inactive students.
     *
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
