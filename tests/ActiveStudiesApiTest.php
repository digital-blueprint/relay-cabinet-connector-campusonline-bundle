<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\ActiveStudiesApi\ActiveStudiesApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\ActiveStudiesApi\StudyStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ActiveStudiesApiTest extends TestCase
{
    private ActiveStudiesApi $api;

    public function setUp(): void
    {
        parent::setUp();
        $config = new ConfigurationService();
        $config->setConfig([
            'api_url' => '',
            'client_id' => '',
            'client_secret' => '',
            'data_service_name_active_studies' => '',
        ]);
        $this->api = new ActiveStudiesApi($config);
        $this->mockResponses([]);
    }

    private function mockResponses(array $responses)
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->api->setClientHandler($stack, 'nope');
    }

    public function testGetActiveStudiesNoneFound()
    {
        $RESPONSE = '
{
    "type": "resources",
    "link": [],
    "resource": []
}
        ';

        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ]);

        $this->assertCount(0, $this->api->getActiveStudies(4242));
    }

    public function testGetActiveStudies()
    {
        $RESPONSE = '
{
    "type": "resources",
    "link": [],
    "resource": [
        {
            "link": [],
            "content": {
                "type": "model-CO_LOC_DS.API_DMS_STUDIEN",
                "API_DMS_STUDIEN": {
                    "STPERSONNR": 123456,
                    "STUDYKEY": "UF 066 921",
                    "STUDYTYPE": "Masterstudium",
                    "STUDYNAME": "Masterstudium; Computer Science",
                    "STUDYSEMESTER": 6,
                    "STUDYSTATUS": "I",
                    "STUDYCURRICULUMVERSION": "2022W",
                    "STUDYIMMATRICULATIONDATE": {
                        "value": "2021-01-01"
                    },
                    "STUDYEXMATRICULATIONDATE": {
                        "value": "2022-01-01"
                    },
                    "STUDYQUALIFICATIONTYPE": "41",
                    "STUDYQUALIFICATIONDATE": {
                        "value": "2010-01-01"
                    },
                    "STUDYQUALIFICATIONSTATENR": 168,
                    "STUDYQUALIFICATIONSTATE": "Österreich"
                }
            }
        }
    ]
}
        ';

        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ]);

        $studies = $this->api->getActiveStudies(123456);
        $this->assertCount(1, $studies);
        $study = $studies[0];
        $this->assertSame(123456, $study->getStudentPersonNumber());
        $this->assertSame('UF 066 921', $study->getStudyKey());
        $this->assertSame('Masterstudium', $study->getStudyType());
        $this->assertSame('Masterstudium; Computer Science', $study->getStudyName());
        $this->assertSame(6, $study->getStudySemester());
        $this->assertSame('2022W', $study->getStudyCurriculumVersion());
        $this->assertSame('2022-01-01', $study->getStudyExmatriculationDate());
        $this->assertSame('2021-01-01', $study->getStudyImmatriculationDate());
        $this->assertSame('2010-01-01', $study->getStudyQualificationDate());
        $this->assertSame('Österreich', $study->getStudyQualificationStateString());
        $this->assertSame('AUT', $study->getStudyQualificationState()->getAlpha3Code());
        $this->assertSame(StudyStatus::Registered, $study->getStudyStatus());
        $this->assertSame('41', $study->getStudyQualificationType()->value);
    }
}
