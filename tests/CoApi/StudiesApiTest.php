<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\CoApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class StudiesApiTest extends TestCase
{
    private CoApi $api;

    public function setUp(): void
    {
        parent::setUp();
        $config = new ConfigurationService();
        $config->setConfig([
            'api_url' => 'https://dummy.at/dummy',
            'client_id' => '',
            'client_secret' => '',
            'data_service_name_studies' => '',
        ]);
        $this->api = new CoApi($config);
        $this->mockResponses([]);
    }

    private function mockResponses(array $responses)
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->api->setClientHandler($stack, 'nope');
    }

    public function testGetStudyWebUrl()
    {
        $study = new Study(['STPERSONNR' => 1234, 'STSTUDIUMNR' => 5678], new \DateTimeZone('Europe/Paris'));
        $this->assertSame('https://dummy.at/dummy/wbStmStudiendaten.wbStudiendetails?pStPersonNr=1234&pStStudiumNr=5678', $this->api->getStudyWebUrl($study));
    }

    public function testGetStudiesNoneFound()
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

        $this->assertCount(0, $this->api->getStudiesApi()->getStudiesForPersonNumber(4242));
    }

    public function testGetStudies()
    {
        $RESPONSE = '
{
    "type": "resources",
    "link": [],
    "resource": [
        {
            "link": [],
            "content": {
                "type": "model-eg.dataservices.tugrazonline",
                "DMSStudies": {
                    "SOURCE": "LiveSync[Single|Normal]",
                    "TIMESTAMP": "13.06.2024T13:08:15",
                    "STSTUDIUMNR": 253324,
                    "STPERSONNR": 123456,
                    "STUDYKEY": "UF 066 921",
                    "STUDYTYPE": "Masterstudium",
                    "STUDYNAME": "Masterstudium; Computer Science",
                    "STUDYSEMESTER": 6,
                    "STUDYSTATUSKEY": "I",
                    "STUDYSTATUS": "gemeldet",
                    "STUDYCURRICULUMVERSION": "2022W",
                    "STUDYIMMATRICULATIONDATE": {
                        "value": "2021-01-01"
                    },
                    "STUDYIMMATRICULATIONSEMESTER": "20S",
                    "STUDYEXMATRICULATIONDATE": {
                        "value": "2022-01-01"
                    },
                    "STUDYEXMATRICULATIONSEMESTER": "24S",
                    "STUDYEXMATRICULATIONTYPEKEY": "EZ",
                    "STUDYEXMATRICULATIONTYPE": "auf Antrag",
                    "STUDYQUALIFICATIONTYPENR": "41",
                    "STUDYQUALIFICATIONTYPE": "Master-/Diplomst.eigene Univ.",
                    "STUDYQUALIFICATIONDATE": {
                        "value": "2010-01-01"
                    },
                    "STUDYQUALIFICATIONSTATENR": 168,
                    "STUDYQUALIFICATIONSTATE": "Österreich",
                    "ADDITIONALCERTIFICATE": "EDG | EGR"
                }
            }
        }
    ]
}
        ';

        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ]);

        $studies = $this->api->getStudiesApi()->getStudiesForPersonNumber(123456);
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
        $this->assertSame('gemeldet', $study->getStudyStatus()->getName());
        $this->assertSame('gemeldet', $study->getStudyStatusString());
        $this->assertSame('41', $study->getStudyQualificationType()->value);
        $this->assertSame('EZ', $study->getStudyExmatriculationType()->value);
        $this->assertSame('ex lege (EZ)', $study->getStudyExmatriculationType()->getName());
        $this->assertSame(2, count($study->getAdditionalCertificate()->items));
        $this->assertSame('EDG', $study->getAdditionalCertificate()->items[0]->value);
        $this->assertSame('EGR', $study->getAdditionalCertificate()->items[1]->value);
        $this->assertSame('gemeldet', $study->getStudyStatusString());
        $this->assertSame('auf Antrag', $study->getStudyExmatriculationTypeString());
        $this->assertSame('20S', $study->getStudyImmatriculationSemester());
        $this->assertSame('24S', $study->getStudyExmatriculationSemester());
        $this->assertSame('Master-/Diplomst.eigene Univ.', $study->getStudyQualificationTypeString());
        $this->assertSame(true, $study->isActive());
        $this->assertSame(true, $study->isLiveData());
        $this->assertSame(1718276895, $study->getSyncDateTime()->getTimestamp());
    }

    public function testGetStudiesMinimal()
    {
        $RESPONSE = '
{
    "type": "resources",
    "link": [],
    "resource": [
        {
            "link": [],
            "content": {
                "type": "model-eg.dataservices.tugrazonline",
                "DMSStudies": {
                    "SOURCE": "LiveSync[Single|Normal]",
                    "TIMESTAMP": "13.06.2024T13:08:15",
                    "STSTUDIUMNR": 253324,
                    "STPERSONNR": 123456,
                    "STUDYKEY": "UF 066 921",
                    "STUDYTYPE": "Masterstudium",
                    "STUDYNAME": "Masterstudium; Computer Science",
                    "STUDYSEMESTER": 6,
                    "STUDYSTATUSKEY": "I",
                    "STUDYSTATUS": "gemeldet",
                    "STUDYCURRICULUMVERSION": null,
                    "STUDYIMMATRICULATIONDATE": {
                        "value": "2021-01-01"
                    },
                    "STUDYIMMATRICULATIONSEMESTER": "20S",
                    "STUDYEXMATRICULATIONDATE": {
                        "value": null
                    },
                    "STUDYEXMATRICULATIONSEMESTER": null,
                    "STUDYEXMATRICULATIONTYPEKEY": null,
                    "STUDYEXMATRICULATIONTYPE": null,
                    "STUDYQUALIFICATIONTYPENR": null,
                    "STUDYQUALIFICATIONTYPE": null,
                    "STUDYQUALIFICATIONDATE": {
                        "value": null
                    },
                    "STUDYQUALIFICATIONSTATENR": null,
                    "STUDYQUALIFICATIONSTATE": null,
                    "ADDITIONALCERTIFICATE": null
                }
            }
        }
    ]
}
        ';

        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ]);

        $studies = $this->api->getStudiesApi()->getStudiesForPersonNumber(123456);
        $this->assertCount(1, $studies);
        $study = $studies[0];
        $this->assertSame(123456, $study->getStudentPersonNumber());
        $this->assertSame('UF 066 921', $study->getStudyKey());
        $this->assertSame('Masterstudium', $study->getStudyType());
        $this->assertSame('Masterstudium; Computer Science', $study->getStudyName());
        $this->assertSame(6, $study->getStudySemester());
        $this->assertSame('2021-01-01', $study->getStudyImmatriculationDate());
        $this->assertSame(null, $study->getStudyCurriculumVersion());
        $this->assertSame(null, $study->getStudyExmatriculationDate());
        $this->assertSame(null, $study->getStudyQualificationDate());
        $this->assertSame(null, $study->getStudyQualificationStateString());
        $this->assertSame(null, $study->getStudyQualificationState());
        $this->assertSame('gemeldet', $study->getStudyStatus()->getName());
        $this->assertSame(null, $study->getStudyQualificationType());
        $this->assertSame(null, $study->getStudyExmatriculationType());
        $this->assertSame([], $study->getAdditionalCertificate()->items);
        $this->assertSame('gemeldet', $study->getStudyStatusString());
        $this->assertSame(null, $study->getStudyExmatriculationTypeString());
        $this->assertSame('20S', $study->getStudyImmatriculationSemester());
        $this->assertSame(null, $study->getStudyExmatriculationSemester());
        $this->assertSame(null, $study->getStudyQualificationTypeString());
        $this->assertSame(false, $study->isActive());
        $this->assertSame(true, $study->isLiveData());
        $this->assertSame(1718276895, $study->getSyncDateTime()->getTimestamp());
    }
}
