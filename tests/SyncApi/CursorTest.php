<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\Application;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\Cursor;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
{
    public function testEncode()
    {
        $cursor = new Cursor();
        $new = Cursor::decode($cursor->encode());
        $this->assertNotNull($new);
    }

    public function testRecordStudent()
    {
        $baseApi = new BaseApi(new Connection('https://dummy.at/dummy', 'foo', 'bar'), 'bla', new \DateTimeZone('Europe/London'));
        $cursor = new Cursor();
        $live = new Student(['SOURCE' => 'LiveSync[Single|Normal]', 'STPERSONNR' => 42, 'TIMESTAMP' => '13.06.2024T11:52:43'], $baseApi);
        $this->assertTrue($live->isLiveData());
        $cursor->recordStudent($live);
        $this->assertNull($cursor->getLastSyncActiveStudents());
        $this->assertFalse($cursor->isStudentOutdated($live));
        $old = new Student(['SOURCE' => 'FOOBAR', 'STPERSONNR' => 42, 'TIMESTAMP' => '13.06.2024T11:52:43'], $baseApi);
        $this->assertTrue($cursor->isStudentOutdated($old));
        $cursor->recordStudent($old);
        $this->assertSame(1718275963, $cursor->getLastSyncActiveStudents()->getTimestamp());
    }

    public function testRecordStudy()
    {
        $baseApi = new BaseApi(new Connection('https://dummy.at/dummy', 'foo', 'bar'), 'bla', new \DateTimeZone('Europe/London'));
        $cursor = new Cursor();
        $live = new Study(['SOURCE' => 'LiveSync[Single|Normal]', 'STSTUDIUMNR' => 42, 'TIMESTAMP' => '13.06.2024T11:52:43'], $baseApi);
        $this->assertTrue($live->isLiveData());
        $cursor->recordStudy($live);
        $this->assertNull($cursor->getLastSyncActiveStudents());
        $this->assertFalse($cursor->isStudyOutdated($live));
        $old = new Study(['SOURCE' => 'FOOBAR', 'STSTUDIUMNR' => 42, 'TIMESTAMP' => '13.06.2024T11:52:43'], $baseApi);
        $this->assertTrue($cursor->isStudyOutdated($old));
        $cursor->recordStudy($old);
        $this->assertSame(1718275963, $cursor->getLastSyncActiveStudies()->getTimestamp());
    }

    public function testRecordApplication()
    {
        $baseApi = new BaseApi(new Connection('https://dummy.at/dummy', 'foo', 'bar'), 'bla', new \DateTimeZone('Europe/London'));
        $cursor = new Cursor();
        $live = new Application(['SOURCE' => 'LiveSync[Single|Normal]', 'BEWERBUNGNR' => 42, 'TIMESTAMP' => '13.06.2024T11:52:43'], $baseApi);
        $this->assertTrue($live->isLiveData());
        $cursor->recordApplication($live);
        $this->assertNull($cursor->getLastSyncApplications());
        $this->assertFalse($cursor->isApplicationOutdated($live));
        $old = new Application(['SOURCE' => 'FOOBAR', 'BEWERBUNGNR' => 42, 'TIMESTAMP' => '13.06.2024T11:52:43'], $baseApi);
        $this->assertTrue($cursor->isApplicationOutdated($old));
        $cursor->recordApplication($old);
        $this->assertSame(1718275963, $cursor->getLastSyncApplications()->getTimestamp());
    }

    public function testStartFinish()
    {
        $old = new Cursor();
        $new = new Cursor($old);
        $new->finish($old);
        $this->assertTrue(true);
    }

    public function testFinishCleanup()
    {
        $baseApi = new BaseApi(new Connection('https://dummy.at/dummy', 'foo', 'bar'), 'bla', new \DateTimeZone('Europe/London'));
        $cursor = new Cursor();
        $live = new Student(['SOURCE' => 'LiveSync[Single|Normal]', 'STPERSONNR' => 42, 'TIMESTAMP' => '12.06.2024T11:52:43'], $baseApi);
        $this->assertTrue($live->isLiveData());
        $cursor->recordStudent($live);
        $this->assertNull($cursor->getLastSyncActiveStudents());
        $this->assertFalse($cursor->isStudentOutdated($live));
        $newer = new Student(['SOURCE' => 'FOOBAR', 'STPERSONNR' => 42, 'TIMESTAMP' => '13.06.2024T11:52:43'], $baseApi);
        $this->assertFalse($cursor->isStudentOutdated($newer));
        $cursor->recordStudent($newer);
        $cursor->finish(null);
    }
}
