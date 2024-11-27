<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\CoApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ApplicationsApiTest extends TestCase
{
    private CoApi $api;

    private const RESPONSE_EMPTY = '
{
    "type": "resources",
    "link": [],
    "resource": []
}
        ';

    private const RESPONSE_FULL = '
{
    "type": "resources",
    "link": [],
    "resource": [
        {
            "link": [],
            "content": {
                "type": "model-eg.dataservices.tugrazonline",
                "DMSApplicants": {
                    "SOURCE": "LiveSync",
                    "TIMESTAMP": "13.06.2024T12:01:15",
                    "BEWERBUNGNR": 12345,
                    "STPERSONNR": 123456,
                    "STSTUDIUMNR": null,
                    "APPLICANTSTARTOFSTUDY": "21W",
                    "APPLICANTSTUDYTYPE": "Bachelorstudium",
                    "APPLICANTSTUDYNAME": "Bachelorstudium; Physik",
                    "APPLICANTSTUDYKEY": "UF 033 678",
                    "APPLICANTQUALIFICATIONTYPE": "25 - ausländische Reifeprüfung",
                    "APPLICANTQUALIFICATIONDATE": {
                        "value": "1970-01-01"
                    },
                    "APPLICANTQUALIFICATIONSTATENR": 40,
                    "APPLICANTQUALIFICATIONSTATE": "Bosnien und Herzegowina"
                }
            }
        }
    ]
}
        ';

    public function setUp(): void
    {
        parent::setUp();
        $config = new ConfigurationService();
        $config->setConfig([
            'api_url' => '',
            'api_time_zone' => 'Europe/Vienna',
            'client_id' => '',
            'client_secret' => '',
            'data_service_name_applications' => '',
        ]);
        $this->api = new CoApi($config);
        $this->mockResponses([]);
    }

    private function mockResponses(array $responses)
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->api->setClientHandler($stack, 'nope');
    }

    public function testGetApplicationsNone()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
        ]);

        $this->assertCount(0, $this->api->getApplicationsApi()->getApplicationsForPersonNumber(4242));
    }

    public function testGetAllApplications()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_FULL),
        ]);

        $this->assertCount(0, $this->api->getApplicationsApi()->getAllApplications());
        $this->assertCount(1, $this->api->getApplicationsApi()->getAllApplications());
    }

    public function testGetChangedApplicationsSince()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_FULL),
        ]);

        $this->assertCount(0, $this->api->getApplicationsApi()->getChangedApplicationsSince(new \DateTimeImmutable()));
        $this->assertCount(1, $this->api->getApplicationsApi()->getChangedApplicationsSince(new \DateTimeImmutable()));
    }

    public function testGetApplicationFull()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_FULL),
        ]);

        $applicationsApi = $this->api->getApplicationsApi();
        $applications = $applicationsApi->getApplicationsForPersonNumber(123456);
        $this->assertCount(1, $applications);
        $application = $applications[0];
        $this->assertSame(12345, $application->getApplicationNumber());
        $this->assertSame('Bachelorstudium; Physik', $application->getStudyName());
        $this->assertSame('Bachelorstudium', $application->getStudyType());
        $this->assertSame('foreign secondary school leaving exam', $application->getQualification()->getName());
        $this->assertSame(123456, $application->getStudentPersonNumber());
        $this->assertSame(null, $application->getStudyNumber());
        $this->assertSame('13.06.2024T12:01:15', $application->getSyncTimestampString());
        $this->assertSame(1718272875, $application->getSyncTimestamp()->getTimestamp());
        $this->assertSame('LiveSync', $application->getSyncSource());
        $this->assertSame('1970-01-01', $application->getQualificationCertificateDate());
        $this->assertSame('BIH', $application->getQualificationIssuingCountry()->getAlpha3Code());
        $this->assertSame('21W', $application->getStartSemester());
        $this->assertSame('Bosnien und Herzegowina', $application->getQualificationIssuingCountryString());
        $this->assertSame('UF 033 678', $application->getStudyKey());
        $this->assertSame(true, $application->isLiveData());
    }
}
