<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class SyncApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private CoApi $coApi;

    public function __construct(CoApi $coApi)
    {
        $this->coApi = $coApi;
        $this->logger = new NullLogger();
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
        // FIXME: This can return data that is older then the one fetched via getSingle*()
        // FIXME: We have to keep track of the IDs and sync timestamps somehow and filter those out
        $this->logger->info('Starting a partial sync');
        $api = $this->coApi;
        $oldCursor = Cursor::decode($newCursor);
        if ($oldCursor->lastSyncActiveStudents === null || $oldCursor->lastSyncActiveStudies === null || $oldCursor->lastSyncActiveStudies === null) {
            throw new \RuntimeException('invalid cursor');
        }

        $newCursor = new Cursor();

        // Get all students that have changed
        $this->logger->info('Checking for changed students (sync timestamp: '.$oldCursor->lastSyncActiveStudents.')');
        $changedStudents = [];
        foreach ($api->getStudentsApi()->getActiveStudents($oldCursor->lastSyncActiveStudents) as $student) {
            Utils::updateMinSyncTimestamp($student, $newCursor->lastSyncActiveStudents);
            $changedStudents[$student->getStudentPersonNumber()] = $student;
        }
        $this->logger->info(count($changedStudents).' changed students');
        // If there were no updates, keep the old sync date
        $newCursor->lastSyncActiveStudents ??= $oldCursor->lastSyncActiveStudents;

        // Get all students for applications that have changed
        $this->logger->info('Checking for changed applications (sync timestamp: '.$oldCursor->lastSyncApplications.')');
        $changedApplicationsCount = 0;
        $changedApplicationStudentsCount = 0;
        foreach ($api->getApplicationsApi()->getAllApplications($oldCursor->lastSyncApplications) as $nr => $entries) {
            ++$changedApplicationStudentsCount;
            foreach ($entries as $application) {
                ++$changedApplicationsCount;
                Utils::updateMinSyncTimestamp($application, $newCursor->lastSyncApplications);
            }
            if (!array_key_exists($nr, $changedStudents)) {
                $changedStudents[$nr] = null;
            }
        }
        $this->logger->info($changedApplicationsCount.' changed applications affecting '.$changedApplicationStudentsCount.' students');
        // If there were no updates, keep the old sync date
        $newCursor->lastSyncApplications ??= $oldCursor->lastSyncApplications;

        // Get all students for studies that have changed
        $this->logger->info('Checking for changed studies (sync timestamp: '.$oldCursor->lastSyncActiveStudies.')');
        $changedStudiesCount = 0;
        $changedStudyStudentsCount = 0;
        foreach ($api->getStudiesApi()->getActiveStudies($oldCursor->lastSyncActiveStudies) as $nr => $entries) {
            ++$changedStudyStudentsCount;
            foreach ($entries as $study) {
                ++$changedStudiesCount;
                Utils::updateMinSyncTimestamp($study, $newCursor->lastSyncActiveStudies);
            }
            if (!array_key_exists($nr, $changedStudents)) {
                $changedStudents[$nr] = null;
            }
        }
        $this->logger->info($changedStudiesCount.' changed studies affecting '.$changedStudyStudentsCount.' students');
        // If there were no updates, keep the old sync date
        $newCursor->lastSyncActiveStudies ??= $oldCursor->lastSyncActiveStudies;

        // FIXME: if there are too many requests to be made here we should fall back to a full sync
        // FIXME: We have to figure out what a reasonable threshold is.
        $this->logger->info('Fetching related data for all '.count($changedStudents).' affected students');
        $res = [];
        foreach ($changedStudents as $nr => $student) {
            if ($student === null) {
                $student = $api->getStudentsApi()->getStudentForPersonNumber($nr);
            }
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

        $this->logger->info('Starting a full sync');

        $getMinForMapping = function (array $mapping, ?string &$min): void {
            foreach ($mapping as $entries) {
                foreach ($entries as $entry) {
                    Utils::updateMinSyncTimestamp($entry, $min);
                }
            }
        };

        $this->logger->info('Fetching all applications');
        $applications = $api->getApplicationsApi()->getAllApplications();
        $getMinForMapping($applications, $cursor->lastSyncApplications);
        $this->logger->info(count($applications).' applications received (sync timestamp: '.$cursor->lastSyncApplications.')');

        $this->logger->info('Fetching all active studies');
        $activeStudies = $api->getStudiesApi()->getActiveStudies();
        $getMinForMapping($activeStudies, $cursor->lastSyncActiveStudies);
        $this->logger->info(count($activeStudies).' active studies received (sync timestamp: '.$cursor->lastSyncActiveStudies.')');
        $inactiveStudies = [];
        if (!$excludeInactive) {
            $inactiveStudies = $api->getStudiesApi()->getInactiveStudies($pageSize);
        } else {
            $this->logger->info('Inactive studies disabled via the config, skipping');
        }

        $this->logger->info('Fetching all active students');
        $res = [];
        $activeStudentCount = 0;
        foreach ($api->getStudentsApi()->getActiveStudents() as $student) {
            ++$activeStudentCount;
            Utils::updateMinSyncTimestamp($student, $cursor->lastSyncActiveStudents);
            $nr = $student->getStudentPersonNumber();
            $studentApplications = $applications[$nr] ?? [];
            $studentStudies = $activeStudies[$nr] ?? [];
            $studentStudies = array_merge($studentStudies, $inactiveStudies[$nr] ?? []);
            $res[] = JsonConverter::convertToJsonObject($student, $studentStudies, $studentApplications);
        }
        $this->logger->info($activeStudentCount.' active students received (sync timestamp: '.$cursor->lastSyncActiveStudents.')');

        if (!$excludeInactive) {
            $this->logger->info('Fetching all inactive students');
            $inactiveStudentCount = 0;
            foreach ($api->getStudentsApi()->getInactiveStudents($pageSize) as $student) {
                ++$inactiveStudentCount;
                $nr = $student->getStudentPersonNumber();
                $studentApplications = $applications[$nr] ?? [];
                $studentStudies = $activeStudies[$nr] ?? [];
                $studentStudies = array_merge($studentStudies, $inactiveStudies[$nr] ?? []);
                $res[$student->getIdentNumberObfuscated()] = JsonConverter::convertToJsonObject($student, $studentStudies, $studentApplications);
            }
            $this->logger->info($inactiveStudentCount.' inactive students received');
        } else {
            $this->logger->info('Inactive students disabled via the config, skipping');
        }

        // Since the sync takes so long and things might have changed since the start, we trigger one
        // incremental sync right away
        $incrementalSyncResult = $this->getAllSince($cursor->encode());
        $cursor = $incrementalSyncResult->getCursor();
        $res = array_merge($res, $incrementalSyncResult->getPersons());

        return new SyncResult($res, $cursor);
    }
}
