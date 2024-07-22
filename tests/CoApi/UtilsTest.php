<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\CoApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testSyncTimestampToDateTimeUTC()
    {
        $dataTime = Utils::syncTimestampToDateTimeUTC('13.06.2024T12:01:15', new \DateTimeZone('Europe/Vienna'));
        $this->assertSame('2024-06-13T10:01:15+00:00', $dataTime->format(\DateTimeInterface::ATOM));
        $this->assertSame('13.06.2024T12:01:15', Utils::dateTimeToSyncTimestamp($dataTime, new \DateTimeZone('Europe/Vienna')));
    }
}
