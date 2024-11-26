<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\CoApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class StudentsApiTest extends TestCase
{
    private CoApi $api;

    private const RESPONSE_EMPTY = '
{
    "type": "resources",
    "link": [],
    "resource": []
}
        ';

    private const RESPONSE_MINIMAL = '
{
    "type": "resources",
    "link": [],
    "resource": [
        {
            "link": [],
            "content": {
                "type": "model-eg.dataservices.tugrazonline",
                "DMSStudents": {
                    "SOURCE": "LiveSync[Single|Normal]",
                    "TIMESTAMP": "13.06.2024T14:00:34",
                    "STPERSONNR": 123123,
                    "STUDID": null,
                    "GIVENNAME": "Max",
                    "FAMILYNAME": "Mustermann",
                    "BIRTHDATE": {
                        "value": "1970-01-01"
                    },
                    "IDENTNROBFUSCATED": "F06BCC80D6FC0BDE575B16FB2E3790D5",
                    "NATIONALITYNR": 11,
                    "NATIONALITY": "Ägypten",
                    "NATIONALITYSECONDARYNR": null,
                    "NATIONALITYSECONDARY": null,
                    "ADMISSIONQUALIFICATIONTYPENR": "02",
                    "ADMISSIONQUALIFICATIONTYPE": "Humanistisches Gymnasium",
                    "SCHOOLCERTIFICATEDATE": {
                        "value": null
                    },
                    "ADMISSIONQUALIFICATIONSTATENR": 40,
                    "ADMISSIONQUALIFICATIONSTATE": "Bosnien und Herzegowina",
                    "TELEPHONE": null,
                    "HOMEADDRESSNOTE": null,
                    "HOMEADDRESSSTREET": null,
                    "HOMEADDRESSPLACE": null,
                    "HOMEADDRESSPOSTCODE": null,
                    "HOMEADDRESSREGION": null,
                    "HOMEADDRESSCOUNTRYNR": null,
                    "HOMEADDRESSCOUNTRY": null,
                    "HOMEADDRESSTELEPHONE": null,
                    "STUDADDRESSNOTE": null,
                    "STUDADDRESSSTREET": null,
                    "STUDADDRESSPLACE": null,
                    "STUDADDRESSPOSTCODE": null,
                    "STUDADDRESSREGION": null,
                    "STUDADDRESSCOUNTRYNR": null,
                    "STUDADDRESSCOUNTRY": null,
                    "STUDADDRESSTELEPHONE": null,
                    "EMAILADDRESSTU": null,
                    "EMAILADDRESSCONFIRMED": null,
                    "EMAILADDRESSTEMPORARY": null,
                    "PERSSTATUS": "Voranmeldung",
                    "STUDSTATUSKEY": "E",
                    "STUDSTATUS": "nicht zugelassen",
                    "TUITIONSTATUS": null,
                    "TUITIONEXEMPTIONTYPE": null,
                    "IMMATRICULATIONDATE": {
                        "value": "2010-12-24"
                    },
                    "IMMATRICULATIONSEMESTER": null,
                    "EXMATRICULATIONSTATUSKEY": null,
                    "EXMATRICULATIONSTATUS": null,
                    "EXMATRICULATIONDATE": {
                        "value": null
                    },
                    "EXMATRICULATIONSEMESTER": null,
                    "TERMSTART": null,
                    "TERMEND": null,
                    "ACADEMICTITLEPRECEDING": null,
                    "ACADEMICTITLEFOLLOWING": null,
                    "FORMERFAMILYNAME": null,
                    "SOCIALSECURITYNR": null,
                    "BPK": null,
                    "GENDERKEY": "M",
                    "GENDER": "Männlich",
                    "NOTE": null
                }
            }
        }
    ]
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
                "DMSStudents": {
                    "SOURCE": "LiveSync[Single|Normal]",
                    "TIMESTAMP": "13.06.2024T14:00:34",
                    "STPERSONNR": 123123,
                    "STUDID": "00712345",
                    "GIVENNAME": "Max",
                    "FAMILYNAME": "Mustermann",
                    "BIRTHDATE": {
                        "value": "1970-01-01"
                    },
                    "IDENTNROBFUSCATED": "F06BCC80D6FC0BDE575B16FB2E3790D5",
                    "NATIONALITYNR": 11,
                    "NATIONALITY": "Ägypten",
                    "NATIONALITYSECONDARYNR": 168,
                    "NATIONALITYSECONDARY": "Österreich",
                    "ADMISSIONQUALIFICATIONTYPENR": "02",
                    "ADMISSIONQUALIFICATIONTYPE": "Humanistisches Gymnasium",
                    "SCHOOLCERTIFICATEDATE": {
                        "value": "2010-12-24"
                    },
                    "ADMISSIONQUALIFICATIONSTATENR": 40,
                    "ADMISSIONQUALIFICATIONSTATE": "Bosnien und Herzegowina",
                    "TELEPHONE": "067612345677",
                    "HOMEADDRESSNOTE": "c/o Erika Mustermann",
                    "HOMEADDRESSSTREET": "Hauptstraße 34",
                    "HOMEADDRESSPLACE": "Altenmarkt bei Sankt Gallen",
                    "HOMEADDRESSPOSTCODE": "8934",
                    "HOMEADDRESSREGION": "Steiermark",
                    "HOMEADDRESSCOUNTRYNR": 11,
                    "HOMEADDRESSCOUNTRY": "Ägypten",
                    "HOMEADDRESSTELEPHONE": "067612345678",
                    "STUDADDRESSNOTE": "c/o Erika Mustermann",
                    "STUDADDRESSSTREET": "Hauptstraße 42",
                    "STUDADDRESSPLACE": "Waizenkirchen",
                    "STUDADDRESSPOSTCODE": "4730",
                    "STUDADDRESSREGION": "Steiermark",
                    "STUDADDRESSCOUNTRYNR": 11,
                    "STUDADDRESSCOUNTRY": "Ägypten",
                    "STUDADDRESSTELEPHONE": "067612345676",
                    "EMAILADDRESSTU": "max.mustermann@student.tugraz.at",
                    "EMAILADDRESSCONFIRMED": "max.mustermann@example.com",
                    "EMAILADDRESSTEMPORARY": "max.mustermann.temp@example.com",
                    "PERSSTATUS": "Voranmeldung",
                    "STUDSTATUSKEY": "E",
                    "STUDSTATUS": "nicht zugelassen",
                    "TUITIONSTATUS": "Ausländer gleichgestellt",
                    "TUITIONEXEMPTIONTYPE": "L Lehrgang",
                    "IMMATRICULATIONDATE": {
                        "value": "2010-12-24"
                    },
                    "IMMATRICULATIONSEMESTER": "21W",
                    "EXMATRICULATIONSTATUSKEY": "EZ",
                    "EXMATRICULATIONSTATUS": "ex lege",
                    "EXMATRICULATIONDATE": {
                        "value": "2023-10-31"
                    },
                    "EXMATRICULATIONSEMESTER": "20W",
                    "TERMSTART": "23W",
                    "TERMEND": "24S",
                    "ACADEMICTITLEPRECEDING": "Ing.",
                    "ACADEMICTITLEFOLLOWING": "Bsc",
                    "FORMERFAMILYNAME": "Normalverbraucher",
                    "SOCIALSECURITYNR": "1223010170",
                    "BPK": "Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=",
                    "GENDERKEY": "M",
                    "GENDER": "Männlich",
                    "NOTE": "some note"
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
            'client_id' => '',
            'client_secret' => '',
            'data_service_name_students' => '',
        ]);
        $this->api = new CoApi($config);
        $this->mockResponses([]);
    }

    public function testGetStudentWebUrl()
    {
        $baseApi = new BaseApi(new Connection('https://dummy.at/dummy', 'foo', 'bar'), 'bla', new \DateTimeZone('Europe/London'));
        $student = new Student(['STPERSONNR' => 1234], $baseApi);
        $this->assertSame('https://dummy.at/dummy/wbStEvidenz.StEvi?pStPersonNr=1234', $student->getWebUrl());
    }

    private function mockResponses(array $responses)
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->api->setClientHandler($stack, 'nope');
    }

    public function testGetStudentForObfuscatedId()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_MINIMAL),
        ]);

        $this->assertNull($this->api->getStudentsApi()->getStudentForObfuscatedId('nope'));
        $this->assertNotNull($this->api->getStudentsApi()->getStudentForObfuscatedId('nope'));
    }

    public function testGetStudentPersonNumber()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_MINIMAL),
        ]);

        $this->assertNull($this->api->getStudentsApi()->getStudentForPersonNumber(1234));
        $this->assertNotNull($this->api->getStudentsApi()->getStudentForPersonNumber(1234));
    }

    public function testGetActiveStudents()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_MINIMAL),
        ]);

        $this->assertCount(0, iterator_to_array($this->api->getStudentsApi()->getActiveStudents()));
        $this->assertCount(1, iterator_to_array($this->api->getStudentsApi()->getActiveStudents()));
    }

    public function testGetChangedStudentsSince()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_MINIMAL),
        ]);

        $this->assertCount(0, iterator_to_array($this->api->getStudentsApi()->getChangedStudentsSince(new \DateTimeImmutable())));
        $this->assertCount(1, iterator_to_array($this->api->getStudentsApi()->getChangedStudentsSince(new \DateTimeImmutable())));
    }

    public function testGetInactiveStudents()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_MINIMAL),
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
        ]);

        $this->assertCount(0, iterator_to_array($this->api->getStudentsApi()->getInactiveStudents(40000)));
        $this->assertCount(1, iterator_to_array($this->api->getStudentsApi()->getInactiveStudents(40000)));
    }

    public function testGetStudentFull()
    {
        $RESPONSE = self::RESPONSE_FULL;

        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ]);

        $data = $this->api->getStudentsApi()->getStudentForObfuscatedId('F06BCC80D6FC0BDE575B16FB2E3790D5');

        $this->assertSame('LiveSync[Single|Normal]', $data->getSyncSource());
        $this->assertSame('13.06.2024T14:00:34', $data->getSyncTimestampString());

        $this->assertSame('1970-01-01', $data->getBirthDate());
        $this->assertSame('male', $data->getGender()->getName());
        $this->assertSame('Männlich', $data->getGenderString());
        $this->assertSame('Ägypten', $data->getNationalityString());
        $this->assertSame(123123, $data->getStudentPersonNumber());

        $this->assertSame('00712345', $data->getStudentId());
        $this->assertSame('Max', $data->getGivenName());
        $this->assertSame('Mustermann', $data->getFamilyName());
        $this->assertSame('F06BCC80D6FC0BDE575B16FB2E3790D5', $data->getIdentNumberObfuscated());
        $this->assertSame(11, $data->getNationality()->value);
        $this->assertSame(168, $data->getNationalitySecondary()->value);
        $this->assertSame('Österreich', $data->getNationalitySecondaryString());
        $this->assertSame('02', $data->getAdmissionQualificationType()->value);
        $this->assertSame('Humanistisches Gymnasium', $data->getAdmissionQualificationTypeString());
        $this->assertSame('2010-12-24', $data->getSchoolCertificateDate());
        $this->assertSame('c/o Erika Mustermann', $data->getHomeAddressNote());
        $this->assertSame('Hauptstraße 34', $data->getHomeAddressStreet());
        $this->assertSame('Altenmarkt bei Sankt Gallen', $data->getHomeAddressPlace());
        $this->assertSame('8934', $data->getHomeAddressPostCode());
        $this->assertSame('EGY', $data->getHomeAddressCountry()->getAlpha3Code());
        $this->assertSame('c/o Erika Mustermann', $data->getStudyAddressNote());
        $this->assertSame('Hauptstraße 42', $data->getStudyAddressStreet());
        $this->assertSame('Waizenkirchen', $data->getStudyAddressPlace());
        $this->assertSame('4730', $data->getStudyAddressPostCode());
        $this->assertSame('EGY', $data->getStudyAddressCountry()->getAlpha3Code());
        $this->assertSame('max.mustermann@student.tugraz.at', $data->getEmailAddressUniversity());
        $this->assertSame('max.mustermann@example.com', $data->getEmailAddressConfirmed());
        $this->assertSame('max.mustermann@example.com', $data->getEmailAddressConfirmed());
        $this->assertSame('pre-registration', $data->getPersonalStatus()->getName());
        $this->assertSame('not admitted', $data->getStudentStatus()->getName());
        $this->assertSame('nicht zugelassen', $data->getStudentStatusString());
        $this->assertSame('2010-12-24', $data->getImmatriculationDate());
        $this->assertSame('ex lege (EZ)', $data->getExmatriculationStatus()->getName());
        $this->assertSame('ex lege', $data->getExmatriculationStatusString());
        $this->assertSame('2023-10-31', $data->getExmatriculationDate());
        $this->assertSame('20W', $data->getExmatriculationSemester());
        $this->assertSame('Ing.', $data->getAcademicTitlePreceding());
        $this->assertSame('Bsc', $data->getAcademicTitleFollowing());
        $this->assertSame('Normalverbraucher', $data->getFormerFamilyName());
        $this->assertSame('1223010170', $data->getSocialSecurityNumber());
        $this->assertSame('Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=', $data->getSectorSpecificPersonalIdentifier());
        $this->assertSame('max.mustermann.temp@example.com', $data->getEmailAddressTemporary());
        $this->assertSame('23W', $data->getStudyLimitStartSemester());
        $this->assertSame('24S', $data->getStudyLimitEndSemester());
        $this->assertSame('067612345677', $data->getTelephoneNumber());
        $this->assertSame('067612345678', $data->getHomeAddressTelephoneNumber());
        $this->assertSame('067612345676', $data->getStudyAddressTelephoneNumber());
        $this->assertSame('BIH', $data->getAdmissionQualificationState()->getAlpha3Code());
        $this->assertSame('Ausländer gleichgestellt', $data->getTuitionStatus());
        $this->assertSame('L Lehrgang', $data->getTuitionExemptionType());
        $this->assertSame('Bosnien und Herzegowina', $data->getAdmissionQualificationStateString());
        $this->assertSame('Ägypten', $data->getHomeAddressCountryString());
        $this->assertSame('Ägypten', $data->getStudyAddressCountryString());
        $this->assertSame('21W', $data->getImmatriculationSemester());
        $this->assertSame('some note', $data->getNote());
        $this->assertSame(false, $data->isActive());
        $this->assertSame(1718280034, $data->getSyncTimestamp()->getTimestamp());
        $this->assertSame(true, $data->isLiveData());
        $this->assertSame('Steiermark', $data->getHomeAddressRegion());
        $this->assertSame('Steiermark', $data->getStudyAddressRegion());
    }

    public function testGetStudentMinimal()
    {
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_MINIMAL),
        ]);

        $data = $this->api->getStudentsApi()->getStudentForObfuscatedId('F06BCC80D6FC0BDE575B16FB2E3790D5');

        // These seem to be always present (based on manual testing only)
        $this->assertSame('1970-01-01', $data->getBirthDate());
        $this->assertSame('male', $data->getGender()->getName());
        $this->assertSame('Männlich', $data->getGenderString());
        $this->assertSame(11, $data->getNationality()->value);
        $this->assertSame('Ägypten', $data->getNationalityString());
        $this->assertSame(123123, $data->getStudentPersonNumber());
        $this->assertSame('Max', $data->getGivenName());
        $this->assertSame('Mustermann', $data->getFamilyName());
        $this->assertSame('F06BCC80D6FC0BDE575B16FB2E3790D5', $data->getIdentNumberObfuscated());
        $this->assertSame('02', $data->getAdmissionQualificationType()->value);
        $this->assertSame('Humanistisches Gymnasium', $data->getAdmissionQualificationTypeString());
        $this->assertSame('pre-registration', $data->getPersonalStatus()->getName());
        $this->assertSame('nicht zugelassen', $data->getStudentStatus()->getName('de'));
        $this->assertSame('nicht zugelassen', $data->getStudentStatusString());
        $this->assertSame('2010-12-24', $data->getImmatriculationDate());
        $this->assertSame('BIH', $data->getAdmissionQualificationState()->getAlpha3Code());
        $this->assertSame('Bosnien und Herzegowina', $data->getAdmissionQualificationStateString());
        $this->assertSame(true, $data->isActive());
        $this->assertSame(1718280034, $data->getSyncTimestamp()->getTimestamp());
        $this->assertSame(true, $data->isLiveData());

        // These are all optional
        $this->assertSame(null, $data->getStudentId());
        $this->assertSame(null, $data->getNationalitySecondary());
        $this->assertSame(null, $data->getNationalitySecondaryString());
        $this->assertSame(null, $data->getSchoolCertificateDate());
        $this->assertSame(null, $data->getHomeAddressNote());
        $this->assertSame(null, $data->getHomeAddressStreet());
        $this->assertSame(null, $data->getHomeAddressPlace());
        $this->assertSame(null, $data->getHomeAddressPostCode());
        $this->assertSame(null, $data->getHomeAddressCountry());
        $this->assertSame(null, $data->getHomeAddressCountryString());
        $this->assertSame(null, $data->getStudyAddressNote());
        $this->assertSame(null, $data->getStudyAddressStreet());
        $this->assertSame(null, $data->getStudyAddressPlace());
        $this->assertSame(null, $data->getStudyAddressPostCode());
        $this->assertSame(null, $data->getStudyAddressCountry());
        $this->assertSame(null, $data->getStudyAddressCountryString());
        $this->assertSame(null, $data->getEmailAddressUniversity());
        $this->assertSame(null, $data->getEmailAddressConfirmed());
        $this->assertSame(null, $data->getEmailAddressConfirmed());
        $this->assertSame(null, $data->getExmatriculationStatus());
        $this->assertSame(null, $data->getExmatriculationStatusString());
        $this->assertSame(null, $data->getExmatriculationDate());
        $this->assertSame(null, $data->getExmatriculationSemester());
        $this->assertSame(null, $data->getAcademicTitlePreceding());
        $this->assertSame(null, $data->getAcademicTitleFollowing());
        $this->assertSame(null, $data->getFormerFamilyName());
        $this->assertSame(null, $data->getSocialSecurityNumber());
        $this->assertSame(null, $data->getSectorSpecificPersonalIdentifier());
        $this->assertSame(null, $data->getEmailAddressTemporary());
        $this->assertSame(null, $data->getStudyLimitStartSemester());
        $this->assertSame(null, $data->getStudyLimitEndSemester());
        $this->assertSame(null, $data->getTelephoneNumber());
        $this->assertSame(null, $data->getHomeAddressTelephoneNumber());
        $this->assertSame(null, $data->getStudyAddressTelephoneNumber());
        $this->assertSame(null, $data->getTuitionStatus());
        $this->assertSame(null, $data->getTuitionExemptionType());
        $this->assertSame(null, $data->getNote());
        $this->assertSame(null, $data->getImmatriculationSemester());
        $this->assertSame(null, $data->getHomeAddressRegion());
        $this->assertSame(null, $data->getStudyAddressRegion());
    }
}
