<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;

class SyncApi
{
    private CoApi $coApi;

    public function __construct(CoApi $coApi)
    {
        $this->coApi = $coApi;
    }

    private function getSingleForStudent(Student $student): array
    {
        $api = $this->coApi;
        $nr = $student->getStudentPersonNumber();
        $studies = $api->getStudiesApi()->getStudiesForPersonNumber($nr);
        $applications = $api->getApplicationsApi()->getApplicationsForPersonNumber($nr);

        return JsonConverter::convertToJsonObject($student, $studies, $applications);
    }

    /**
     * Return a JSON object for a single student given a StPersonNr.
     */
    public function getSingleForPersonNumber(int $studentPersonNumber): ?array
    {
        $api = $this->coApi;

        $student = $api->getStudentsApi()->getStudentForPersonNumber($studentPersonNumber);
        if ($student === null) {
            return null;
        }

        return $this->getSingleForStudent($student);
    }

    /**
     * Return a JSON object for a single student given a IdentNrObfuscated.
     */
    public function getSingleForObfuscatedId(string $obfuscatedId): ?array
    {
        $api = $this->coApi;

        $student = $api->getStudentsApi()->getStudentForObfuscatedId($obfuscatedId);
        if ($student === null) {
            return null;
        }

        return $this->getSingleForStudent($student);
    }

    /**
     * Can be used after calling getAll() to get incremental updates by passing the cursor
     * returned by getAll().
     */
    public function getAllSince(string $newCursor): SyncResult
    {
        $api = $this->coApi;
        $oldCursor = Cursor::decode($newCursor);
        if ($oldCursor->lastSyncActiveStudents === null || $oldCursor->lastSyncActiveStudies === null || $oldCursor->lastSyncActiveStudies === null) {
            throw new \RuntimeException('invalid cursor');
        }

        $newCursor = new Cursor();

        // Get all students that have changed
        $changedStudents = [];
        foreach ($api->getStudentsApi()->getActiveStudents($oldCursor->lastSyncActiveStudents) as $student) {
            Utils::updateMinSyncTimestamp($student, $newCursor->lastSyncActiveStudents);
            $changedStudents[$student->getStudentPersonNumber()] = $student;
        }
        // If there were no updates, keep the old sync date
        $newCursor->lastSyncActiveStudents ??= $oldCursor->lastSyncActiveStudents;

        // Get all students for applications that have changed
        foreach ($api->getApplicationsApi()->getAllApplications($oldCursor->lastSyncApplications) as $nr => $entries) {
            foreach ($entries as $application) {
                Utils::updateMinSyncTimestamp($application, $newCursor->lastSyncApplications);
            }
            if (!array_key_exists($nr, $changedStudents)) {
                $student = $api->getStudentsApi()->getStudentForPersonNumber($nr);
                $changedStudents[$nr] = $student;
            }
        }
        // If there were no updates, keep the old sync date
        $newCursor->lastSyncApplications ??= $oldCursor->lastSyncApplications;

        // Get all students for studies that have changed
        foreach ($api->getStudiesApi()->getActiveStudies($oldCursor->lastSyncActiveStudies) as $nr => $entries) {
            foreach ($entries as $study) {
                Utils::updateMinSyncTimestamp($study, $newCursor->lastSyncActiveStudies);
            }
            if (!array_key_exists($nr, $changedStudents)) {
                $student = $api->getStudentsApi()->getStudentForPersonNumber($nr);
                $changedStudents[$nr] = $student;
            }
        }
        // If there were no updates, keep the old sync date
        $newCursor->lastSyncActiveStudies ??= $oldCursor->lastSyncActiveStudies;

        $res = [];
        foreach ($changedStudents as $student) {
            $res[$student->getIdentNumberObfuscated()] = $this->getSingleForStudent($student);
        }

        return new SyncResult($res, $newCursor->encode());
    }

    /**
     * @param $excludeInactive - exclude all inactive students and studies
     */
    public function getAll(bool $excludeInactive = true, int $pageSize = 40000): SyncResult
    {
        $api = $this->coApi;
        $cursor = new Cursor();

        $getMinForMapping = function (array $mapping, ?string &$min): void {
            foreach ($mapping as $entries) {
                foreach ($entries as $entry) {
                    Utils::updateMinSyncTimestamp($entry, $min);
                }
            }
        };

        $applications = $api->getApplicationsApi()->getAllApplications();
        $activeStudies = $api->getStudiesApi()->getActiveStudies();
        $inactiveStudies = [];
        if (!$excludeInactive) {
            $inactiveStudies = $api->getStudiesApi()->getInactiveStudies($pageSize);
        }

        $getMinForMapping($applications, $cursor->lastSyncApplications);
        $getMinForMapping($activeStudies, $cursor->lastSyncActiveStudies);

        $res = [];
        foreach ($api->getStudentsApi()->getActiveStudents() as $student) {
            Utils::updateMinSyncTimestamp($student, $cursor->lastSyncActiveStudents);
            $nr = $student->getStudentPersonNumber();
            $studentApplications = $applications[$nr] ?? [];
            $studentStudies = $activeStudies[$nr] ?? [];
            $studentStudies = array_merge($studentStudies, $inactiveStudies[$nr] ?? []);
            $res[] = JsonConverter::convertToJsonObject($student, $studentStudies, $studentApplications);
        }

        if (!$excludeInactive) {
            foreach ($api->getStudentsApi()->getInactiveStudents($pageSize) as $student) {
                $nr = $student->getStudentPersonNumber();
                $studentApplications = $applications[$nr] ?? [];
                $studentStudies = $activeStudies[$nr] ?? [];
                $studentStudies = array_merge($studentStudies, $inactiveStudies[$nr] ?? []);
                $res[$student->getIdentNumberObfuscated()] = JsonConverter::convertToJsonObject($student, $studentStudies, $studentApplications);
            }
        }

        // Since the sync takes so long and things might have changed since the start, we trigger one
        // incremental sync right away
        $incrementalSyncResult = $this->getAllSince($cursor->encode());
        $cursor = $incrementalSyncResult->getCursor();
        $res = array_merge($res, $incrementalSyncResult->getResults());

        return new SyncResult($res, $cursor);
    }
}
