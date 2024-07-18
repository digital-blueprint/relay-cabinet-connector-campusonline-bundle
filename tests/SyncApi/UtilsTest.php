<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testCompareSyncTimestamps()
    {
        $this->assertSame(0, Utils::compareSyncTimestamps('13.06.2024T12:01:15', '13.06.2024T12:01:15'));
        $this->assertSame(-1, Utils::compareSyncTimestamps('14.06.2023T12:01:15', '13.06.2024T12:01:15'));
        $this->assertSame(1, Utils::compareSyncTimestamps('12.06.2025T12:01:15', '13.06.2024T12:01:15'));
    }
}
