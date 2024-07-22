<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

class Cursor
{
    public ?\DateTimeInterface $lastSyncApplications = null;

    public ?\DateTimeInterface $lastSyncActiveStudies = null;

    public ?\DateTimeInterface $lastSyncActiveStudents = null;

    public function encode(): string
    {
        return serialize($this);
    }

    public static function decode(string $value): Cursor
    {
        return unserialize($value);
    }
}
