<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class Test extends KernelTestCase
{
    public function testContainer()
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->assertNotNull($container);
    }

    public function testSourceTimestampToIso()
    {
        $this->assertSame('2024-06-13T12:01:15', Utils::sourceTimestampToIso('13.06.2024T12:01:15'));
    }
}
