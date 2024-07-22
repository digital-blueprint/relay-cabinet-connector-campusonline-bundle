<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\Application;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;

class Cursor
{
    public ?\DateTimeInterface $lastSyncApplications = null;

    public ?\DateTimeInterface $lastSyncActiveStudies = null;

    public ?\DateTimeInterface $lastSyncActiveStudents = null;

    /**
     * A mapping of StudentPersonNumber to the timestamp of the live data we got.
     *
     * @var array<int, \DateTimeInterface>
     */
    protected array $liveStudents = [];

    /**
     * A mapping of StudentPersonNumber to the timestamp of the live data we got.
     *
     * @var array<int, \DateTimeInterface>
     */
    protected array $liveStudies = [];

    /**
     * A mapping of StudentPersonNumber to the timestamp of the live data we got.
     *
     * @var array<int, \DateTimeInterface>
     */
    protected array $liveApplications = [];

    public function __construct(?Cursor $oldCursor = null)
    {
        if ($oldCursor !== null) {
            $this->liveStudents = $oldCursor->liveStudents;
            $this->liveStudies = $oldCursor->liveStudies;
            $this->liveApplications = $oldCursor->liveApplications;
        }
    }

    /**
     * Returns whether the student was already seen with a newer sync timestamp. Which means this record needs to
     * be ignored and not passed to the client.
     */
    public function isStudentOutdated(Student $student): bool
    {
        if ($student->isLiveData()) {
            return false;
        }
        $liveTimestamp = $this->liveStudents[$student->getStudentPersonNumber()] ?? null;

        return $liveTimestamp !== null && $student->getSyncTimestamp() <= $liveTimestamp;
    }

    /**
     * Returns whether the study was already seen with a newer sync timestamp. Which means this record needs to
     * be ignored and not passed to the client.
     */
    public function isStudyOutdated(Study $study): bool
    {
        if ($study->isLiveData()) {
            return false;
        }
        $liveTimestamp = $this->liveStudies[$study->getStudyNumber()] ?? null;

        return $liveTimestamp !== null && $study->getSyncTimestamp() <= $liveTimestamp;
    }

    /**
     * Returns whether the application was already seen with a newer sync timestamp. Which means this record needs to
     * be ignored and not passed to the client.
     */
    public function isApplicationOutdated(Application $application): bool
    {
        if ($application->isLiveData()) {
            return false;
        }
        $liveTimestamp = $this->liveApplications[$application->getApplicationNumber()] ?? null;

        return $liveTimestamp !== null && $application->getSyncTimestamp() <= $liveTimestamp;
    }

    public function recordStudent(Student $student): void
    {
        if ($student->isLiveData()) {
            $this->liveStudents[$student->getStudentPersonNumber()] = $student->getSyncTimestamp();
        } elseif ($this->lastSyncActiveStudents === null) {
            $this->lastSyncActiveStudents = $student->getSyncTimestamp();
        }
    }

    public function recordStudy(Study $study): void
    {
        if ($study->isLiveData()) {
            $this->liveStudies[$study->getStudyNumber()] = $study->getSyncTimestamp();
        } elseif ($this->lastSyncActiveStudies === null) {
            $this->lastSyncActiveStudies = $study->getSyncTimestamp();
        }
    }

    public function recordApplication(Application $application): void
    {
        if ($application->isLiveData()) {
            $this->liveApplications[$application->getStudentPersonNumber()] = $application->getSyncTimestamp();
        } elseif ($this->lastSyncApplications === null) {
            $this->lastSyncApplications = $application->getSyncTimestamp();
        }
    }

    public function finish(?Cursor $oldCursor = null): void
    {
        if ($oldCursor !== null) {
            // If there were no updates, keep the old sync date
            $this->lastSyncActiveStudents ??= $oldCursor->lastSyncActiveStudents;
            $this->lastSyncApplications ??= $oldCursor->lastSyncApplications;
            $this->lastSyncActiveStudies ??= $oldCursor->lastSyncActiveStudies;
        }

        // While at it, remove outdated entries.
        // Records of live students which are older than the last non-live sync date
        if ($this->lastSyncActiveStudents !== null) {
            $this->liveStudents = array_filter($this->liveStudents, function (\DateTimeInterface $timestamp) {
                return $timestamp->getTimestamp() > $this->lastSyncActiveStudents->getTimestamp();
            });
        }
        if ($this->lastSyncActiveStudies !== null) {
            $this->liveStudies = array_filter($this->liveStudies, function (\DateTimeInterface $timestamp) {
                return $timestamp->getTimestamp() > $this->lastSyncActiveStudies->getTimestamp();
            });
        }
        if ($this->lastSyncApplications !== null) {
            $this->liveApplications = array_filter($this->liveApplications, function (\DateTimeInterface $timestamp) {
                return $timestamp->getTimestamp() > $this->lastSyncApplications->getTimestamp();
            });
        }
    }

    public function encode(): string
    {
        return serialize($this);
    }

    public static function decode(string $value): Cursor
    {
        return unserialize($value);
    }
}
