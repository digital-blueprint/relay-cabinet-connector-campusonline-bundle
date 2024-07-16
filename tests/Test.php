<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils as CoUtils;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\Utils as SyncUtils;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class Test extends KernelTestCase
{
    public function testContainer()
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->assertNotNull($container);
    }

    public function testSyncTimestampToDateTimeUTC()
    {
        $dataTime = CoUtils::syncTimestampToDateTimeUTC('13.06.2024T12:01:15', new \DateTimeZone('Europe/Vienna'));
        $this->assertSame('2024-06-13T10:01:15+00:00', $dataTime->format(\DateTimeInterface::ATOM));
    }

    public function testCompareSyncTimestamps()
    {
        $this->assertSame(0, SyncUtils::compareSyncTimestamps('13.06.2024T12:01:15', '13.06.2024T12:01:15'));
        $this->assertSame(-1, SyncUtils::compareSyncTimestamps('14.06.2023T12:01:15', '13.06.2024T12:01:15'));
        $this->assertSame(1, SyncUtils::compareSyncTimestamps('12.06.2025T12:01:15', '13.06.2024T12:01:15'));
    }
}
