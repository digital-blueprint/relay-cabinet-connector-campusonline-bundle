<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\Application;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;

/**
 * The cursor keeps track of two things for all three APIs:
 *
 * * The last sync date from full/delta sync, so we can continue using delta syncs
 * * All the live data we fetched out-of-band, which is potentially newer than things we get from the full/delta sync,
 *   So we don't go back in time if we do a delta sync after a live sync.
 */
class Cursor
{
    private ?int $lastSyncApplications = null;

    private ?int $lastSyncActiveStudies = null;

    private ?int $lastSyncActiveStudents = null;

    /**
     * A mapping of StudentPersonNumber to the timestamp of the live data we got.
     *
     * @var array<int, int>
     */
    protected array $liveStudents = [];

    /**
     * A mapping of StudentPersonNumber to the timestamp of the live data we got.
     *
     * @var array<int, int>
     */
    protected array $liveStudies = [];

    /**
     * A mapping of StudentPersonNumber to the timestamp of the live data we got.
     *
     * @var array<int, int>
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

        return $liveTimestamp !== null && $student->getSyncTimestamp()->getTimestamp() <= $liveTimestamp;
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

        return $liveTimestamp !== null && $study->getSyncTimestamp()->getTimestamp() <= $liveTimestamp;
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

        return $liveTimestamp !== null && $application->getSyncTimestamp()->getTimestamp() <= $liveTimestamp;
    }

    public function recordStudent(Student $student): void
    {
        if ($student->isLiveData()) {
            $this->liveStudents[$student->getStudentPersonNumber()] = $student->getSyncTimestamp()->getTimestamp();
        } elseif ($this->lastSyncActiveStudents === null) {
            $this->lastSyncActiveStudents = $student->getSyncTimestamp()->getTimestamp();
        }
    }

    public function recordStudy(Study $study): void
    {
        if ($study->isLiveData()) {
            $this->liveStudies[$study->getStudyNumber()] = $study->getSyncTimestamp()->getTimestamp();
        } elseif ($this->lastSyncActiveStudies === null) {
            $this->lastSyncActiveStudies = $study->getSyncTimestamp()->getTimestamp();
        }
    }

    public function recordApplication(Application $application): void
    {
        if ($application->isLiveData()) {
            $this->liveApplications[$application->getApplicationNumber()] = $application->getSyncTimestamp()->getTimestamp();
        } elseif ($this->lastSyncApplications === null) {
            $this->lastSyncApplications = $application->getSyncTimestamp()->getTimestamp();
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
            $this->liveStudents = array_filter($this->liveStudents, function (int $timestamp) {
                return $timestamp > $this->lastSyncActiveStudents;
            });
        }
        if ($this->lastSyncActiveStudies !== null) {
            $this->liveStudies = array_filter($this->liveStudies, function (int $timestamp) {
                return $timestamp > $this->lastSyncActiveStudies;
            });
        }
        if ($this->lastSyncApplications !== null) {
            $this->liveApplications = array_filter($this->liveApplications, function (int $timestamp) {
                return $timestamp > $this->lastSyncApplications;
            });
        }
    }

    public function encode(): string
    {
        $data = [
            'lastSyncApplications' => $this->lastSyncApplications,
            'lastSyncActiveStudies' => $this->lastSyncActiveStudies,
            'lastSyncActiveStudents' => $this->lastSyncActiveStudents,
            'liveStudents' => $this->liveStudents,
            'liveStudies' => $this->liveStudies,
            'liveApplications' => $this->liveApplications,
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public static function decode(string $value): Cursor
    {
        $data = json_decode($value, true, flags: JSON_THROW_ON_ERROR);
        $cursor = new self();

        $cursor->lastSyncApplications = $data['lastSyncApplications'];
        $cursor->lastSyncActiveStudies = $data['lastSyncActiveStudies'];
        $cursor->lastSyncActiveStudents = $data['lastSyncActiveStudents'];
        $cursor->liveStudents = $data['liveStudents'];
        $cursor->liveStudies = $data['liveStudies'];
        $cursor->liveApplications = $data['liveApplications'];

        return $cursor;
    }

    public function getLastSyncApplications(): ?\DateTimeInterface
    {
        return $this->lastSyncApplications !== null ? \DateTimeImmutable::createFromFormat('U', (string) $this->lastSyncApplications) : null;
    }

    public function getLastSyncActiveStudies(): ?\DateTimeInterface
    {
        return $this->lastSyncActiveStudies !== null ? \DateTimeImmutable::createFromFormat('U', (string) $this->lastSyncActiveStudies) : null;
    }

    public function getLastSyncActiveStudents(): ?\DateTimeInterface
    {
        return $this->lastSyncActiveStudents !== null ? \DateTimeImmutable::createFromFormat('U', (string) $this->lastSyncActiveStudents) : null;
    }
}
