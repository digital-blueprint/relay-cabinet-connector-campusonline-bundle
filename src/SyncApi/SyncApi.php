<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\Application;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class SyncApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private CoApi $coApi;
    private bool $excludeInactive;
    private int $pageSize;

    public function __construct(CoApi $coApi, ConfigurationService $config)
    {
        $this->coApi = $coApi;
        $this->logger = new NullLogger();
        $this->excludeInactive = $config->getExcludeInactive();
        $this->pageSize = $config->getPageSize();
    }

    /**
     * Filter out Applications where the associated study is not available, which happens in case we filter out
     * inactive studies. This avoids applications that reference non-existing study objects.
     *
     * @param Application[] $applications
     * @param Study[]       $studies
     *
     * @return Application[]
     */
    private static function filterApplications(array $applications, array $studies): array
    {
        $filtered = [];
        foreach ($applications as $application) {
            $studyNumber = $application->getStudyNumber();
            if ($studyNumber === null) {
                // Not associated with a study, so just keep it.
                $filtered[] = $application;
                continue;
            }
            foreach ($studies as $study) {
                if ($study->getStudyNumber() === $studyNumber) {
                    $filtered[] = $application;
                    continue 2;
                }
            }
        }

        return $filtered;
    }

    private function getSingleForStudent(Student $student, ?Cursor $cursor): array
    {
        $api = $this->coApi;
        $cursor?->recordStudent($student);
        $nr = $student->getStudentPersonNumber();
        $studies = $api->getStudiesApi()->getStudiesForPersonNumber($nr);
        $filteredStudies = [];
        foreach ($studies as $study) {
            if ($this->excludeInactive && !$study->isActive()) {
                continue;
            }
            $cursor?->recordStudy($study);
            $filteredStudies[] = $study;
        }
        $studies = $filteredStudies;
        $applications = $api->getApplicationsApi()->getApplicationsForPersonNumber($nr);
        if ($this->excludeInactive) {
            $applications = self::filterApplications($applications, $studies);
        }
        foreach ($applications as $application) {
            $cursor?->recordApplication($application);
        }

        return JsonConverter::convertToJsonObject($student, $studies, $applications);
    }

    /**
     * Return a JSON object for a single student given a StPersonNr.
     */
    public function getSingleForPersonNumber(int $studentPersonNumber): ?array
    {
        $api = $this->coApi;

        $student = $api->getStudentsApi()->getStudentForPersonNumber($studentPersonNumber);
        if ($this->excludeInactive && !$student->isActive()) {
            return null;
        }
        if ($student === null) {
            return null;
        }

        return $this->getSingleForStudent($student, null);
    }

    /**
     * Return a JSON object for a single student given a IdentNrObfuscated.
     */
    public function getSingleForObfuscatedId(string $obfuscatedId): ?array
    {
        $api = $this->coApi;

        $student = $api->getStudentsApi()->getStudentForObfuscatedId($obfuscatedId);
        if ($this->excludeInactive && !$student->isActive()) {
            return null;
        }
        if ($student === null) {
            return null;
        }

        return $this->getSingleForStudent($student, null);
    }

    public function getSome(array $ids, ?string $cursor = null): SyncResult
    {
        $newCursor = ($cursor !== null) ? Cursor::decode($cursor) : new Cursor();
        $api = $this->coApi->getStudentsApi();

        $res = [];
        foreach ($ids as $id) {
            $student = $api->getStudentForObfuscatedId($id);
            if ($student !== null) {
                $res[$id] = $this->getSingleForStudent($student, $newCursor);
            }
        }

        return new SyncResult($res, $newCursor->encode(), false);
    }

    /**
     * Can be used after calling getAll() to get incremental updates by passing the cursor
     * returned by getAll().
     */
    public function getAllSince(string $newCursor): SyncResult
    {
        $this->logger->info('Starting a partial sync');
        $api = $this->coApi;
        $oldCursor = Cursor::decode($newCursor);
        $newCursor = new Cursor($oldCursor);

        // Get all students that have changed
        $this->logger->info('Checking for changed students', ['sync timestamp' => $oldCursor->lastSyncActiveStudents]);
        $changedStudents = [];
        foreach ($api->getStudentsApi()->getChangedStudentsSince($oldCursor->lastSyncActiveStudents) as $student) {
            $newCursor->recordStudent($student);
            if ($newCursor->isStudentOutdated($student)) {
                continue;
            }
            $changedStudents[$student->getStudentPersonNumber()] = $student;
        }
        $this->logger->info(count($changedStudents).' changed students');

        // Get all students for applications that have changed
        $this->logger->info('Checking for changed applications', ['sync timestamp' => $newCursor->lastSyncApplications]);
        $changedApplicationsCount = 0;
        $changedApplicationStudentsCount = 0;
        foreach ($api->getApplicationsApi()->getChangedApplicationsSince($oldCursor->lastSyncApplications) as $nr => $entries) {
            ++$changedApplicationStudentsCount;
            $anyNotOutdated = false;
            foreach ($entries as $application) {
                ++$changedApplicationsCount;
                $newCursor->recordApplication($application);
                if (!$newCursor->isApplicationOutdated($application)) {
                    $anyNotOutdated = true;
                }
            }
            if ($anyNotOutdated && !array_key_exists($nr, $changedStudents)) {
                $changedStudents[$nr] = null;
            }
        }
        $this->logger->info($changedApplicationsCount.' changed applications affecting '.$changedApplicationStudentsCount.' students');

        // Get all students for studies that have changed
        $this->logger->info('Checking for changed studies', ['sync timestamp' => $oldCursor->lastSyncActiveStudies]);
        $changedStudiesCount = 0;
        $changedStudyStudentsCount = 0;
        foreach ($api->getStudiesApi()->getChangedStudiesSince($oldCursor->lastSyncActiveStudies) as $nr => $entries) {
            ++$changedStudyStudentsCount;
            $anyNotOutdated = false;
            foreach ($entries as $study) {
                ++$changedStudiesCount;
                $newCursor->recordStudy($study);
                if (!$newCursor->isStudyOutdated($study)) {
                    $anyNotOutdated = true;
                }
            }
            if ($anyNotOutdated && !array_key_exists($nr, $changedStudents)) {
                $changedStudents[$nr] = null;
            }
        }
        $this->logger->info($changedStudiesCount.' changed studies affecting '.$changedStudyStudentsCount.' students');

        $this->logger->info('Fetching related data for all '.count($changedStudents).' affected students');
        $res = [];
        foreach ($changedStudents as $nr => $student) {
            if ($student === null) {
                $student = $api->getStudentsApi()->getStudentForPersonNumber($nr);
                $newCursor->recordStudent($student);
            }
            if ($this->excludeInactive && !$student->isActive()) {
                // This can happen if a students transitions from active to inactive, just throw
                // out the update if inactive students should be excluded
                continue;
            }
            if (!$newCursor->isStudentOutdated($student)) {
                $res[$student->getIdentNumberObfuscated()] = $this->getSingleForStudent($student, $newCursor);
            }
        }

        $newCursor->finish($oldCursor);

        return new SyncResult($res, $newCursor->encode(), false);
    }

    private function getAll(int $pageSize): SyncResult
    {
        $api = $this->coApi;
        $cursor = new Cursor();

        $this->logger->info('Starting a full sync');

        $this->logger->info('Fetching all applications');
        $applications = $api->getApplicationsApi()->getAllApplications();
        foreach ($applications as $entries) {
            foreach ($entries as $application) {
                $cursor->recordApplication($application);
            }
        }
        $this->logger->info(count($applications).' applications received', ['sync timestamp' => $cursor->lastSyncApplications]);

        $this->logger->info('Fetching all active studies');
        $activeStudies = $api->getStudiesApi()->getActiveStudies();
        foreach ($activeStudies as $entries) {
            foreach ($entries as $study) {
                $cursor->recordStudy($study);
            }
        }
        $this->logger->info(count($activeStudies).' active studies received', ['sync timestamp' => $cursor->lastSyncActiveStudies]);
        $inactiveStudies = [];
        if (!$this->excludeInactive) {
            $this->logger->info('Fetching all inactive studies');
            $inactiveStudies = $api->getStudiesApi()->getInactiveStudies($pageSize);
        } else {
            $this->logger->info('Inactive studies disabled via the config, skipping');
        }

        // In case we exclude inactive studies also filter out applications related to them,
        // to avoid those relations being broken.
        if ($this->excludeInactive) {
            foreach ($applications as $studId => $entries) {
                $studies = $activeStudies[$studId] ?? [];
                $applications[$studId] = self::filterApplications($entries, $studies);
            }
        }

        $this->logger->info('Fetching all active students');
        $res = [];
        $activeStudentCount = 0;
        foreach ($api->getStudentsApi()->getActiveStudents() as $student) {
            ++$activeStudentCount;
            $cursor->recordStudent($student);
            $nr = $student->getStudentPersonNumber();
            $studentApplications = $applications[$nr] ?? [];
            $studentStudies = $activeStudies[$nr] ?? [];
            $studentStudies = array_merge($studentStudies, $inactiveStudies[$nr] ?? []);
            $res[] = JsonConverter::convertToJsonObject($student, $studentStudies, $studentApplications);
        }
        $this->logger->info($activeStudentCount.' active students received', ['sync timestamp' => $cursor->lastSyncActiveStudents]);

        if (!$this->excludeInactive) {
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

        return new SyncResult($res, $cursor->encode(), true);
    }

    public function getAllFirstTime(): SyncResult
    {
        $result = $this->getAll($this->pageSize);

        // Since the sync takes so long and things might have changed since the start, we trigger one
        // incremental sync right away
        $incrementalSyncResult = $this->getAllSince($result->getCursor());
        $cursor = $incrementalSyncResult->getCursor();
        $res = array_merge($result->getPersons(), $incrementalSyncResult->getPersons());

        return new SyncResult($res, $cursor, true);
    }
}
