<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi\Gender;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi\PersonalStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi\PersonDataApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi\StudentStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PersonDataApiTest extends TestCase
{
    private PersonDataApi $api;

    public function setUp(): void
    {
        parent::setUp();
        $config = new ConfigurationService();
        $config->setConfig([
            'api_url' => '',
            'client_id' => '',
            'client_secret' => '',
            'data_service_name_person_data' => '',
        ]);
        $this->api = new PersonDataApi($config);
        $this->mockResponses([]);
    }

    private function mockResponses(array $responses)
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->api->setClientHandler($stack, 'nope');
    }

    public function testGetPersonDataNotFound()
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

        $this->assertNull($this->api->getPersonData('nope'));
    }

    public function testGetPersonDataFull()
    {
        $RESPONSE = '
{
    "type": "resources",
    "link": [],
    "resource": [
        {
            "link": [],
            "content": {
                "type": "model-CO_LOC_DS.API_DMS_STUD_PERS_MV",
                "API_DMS_STUD_PERS_MV": {
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
                    "HOMEADDRESSNOTE": "c/o Erika Mustermann",
                    "HOMEADDRESSSTREET": "Hauptstraße 34",
                    "HOMEADDRESSPLACE": "Altenmarkt bei Sankt Gallen",
                    "HOMEADDRESSPOSTCODE": "8934",
                    "HOMEADDRESSCOUNTRY": 11,
                    "STUDADDRESSNOTE": "c/o Erika Mustermann",
                    "STUDADDRESSSTREET": "Hauptstraße 42",
                    "STUDADDRESSPLACE": "Waizenkirchen",
                    "STUDADDRESSPOSTCODE": "4730",
                    "STUDADDRESSCOUNTRY": 11,
                    "EMAILADDRESSTU": "max.mustermann@student.tugraz.at",
                    "EMAILADDRESSCONFIRMED": "max.mustermann@example.com",
                    "EMAILADDRESSTEMPORARY": "max.mustermann.temp@example.com",
                    "PERSTUSTATUS": "Voranmeldung",
                    "STUDTUSTATUS": "E",
                    "IMMATRICULATIONDATE": {
                        "value": "2010-12-24"
                    },
                    "EXMATRICULATIONSTATUS": null,
                    "EXMATRICULATIONDATE": {
                        "value": null
                    },
                    "ACADEMICTITLEPRECEDING": "Ing.",
                    "ACADEMICTITLEFOLLOWING": "Bsc",
                    "FORMERFAMILYNAME": "Normalverbraucher",
                    "SOCIALSECURITYNR": "1223010170",
                    "BPK": "Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=",
                    "GENDER": "X"
                }
            }
        }
    ]
}
        ';

        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ]);

        $data = $this->api->getPersonData('F06BCC80D6FC0BDE575B16FB2E3790D5');

        $this->assertSame('1970-01-01', $data->getBirthDate());
        $this->assertSame(Gender::NonBinary, $data->getGender());
        $this->assertSame('Ägypten', $data->getNationalityString());
        $this->assertSame(123123, $data->getStudentPersonNumber());

        $this->assertSame('00712345', $data->getStudentId());
        $this->assertSame('Max', $data->getGivenName());
        $this->assertSame('Mustermann', $data->getFamilyName());
        $this->assertSame('F06BCC80D6FC0BDE575B16FB2E3790D5', $data->getIdentNumberObfuscated());
        $this->assertSame(11, $data->getNationality()->value);
        $this->assertSame(168, $data->getNationalitySecondary()->value);
        $this->assertSame('Österreich', $data->getNationalitySecondaryString());
        $this->assertSame('02', $data->getAdmissionQualificationTypeNumber());
        $this->assertSame('Humanistisches Gymnasium', $data->getAdmissionQualificationType());
        $this->assertSame('2010-12-24', $data->getSchoolCertificateDate());
        $this->assertSame('c/o Erika Mustermann', $data->getHomeAddressNote());
        $this->assertSame('Hauptstraße 34', $data->getHomeAddressStreet());
        $this->assertSame('Altenmarkt bei Sankt Gallen', $data->getHomeAddressPlace());
        $this->assertSame('8934', $data->getHomeAddressPostCode());
        $this->assertSame(11, $data->getHomeAddressCountry());
        $this->assertSame('c/o Erika Mustermann', $data->getStudentAddressNote());
        $this->assertSame('Hauptstraße 42', $data->getStudentAddressStreet());
        $this->assertSame('Waizenkirchen', $data->getStudentAddressPlace());
        $this->assertSame('4730', $data->getStudentAddressPostCode());
        $this->assertSame(11, $data->getStudentAddressCountry());
        $this->assertSame('max.mustermann@student.tugraz.at', $data->getEmailAddressUniversity());
        $this->assertSame('max.mustermann@example.com', $data->getEmailAddressConfirmed());
        $this->assertSame('max.mustermann@example.com', $data->getEmailAddressConfirmed());
        $this->assertSame(PersonalStatus::PreRegistration, $data->getPersonalStatus());
        $this->assertSame(StudentStatus::NotAdmitted, $data->getStudentStatus());
        $this->assertSame('2010-12-24', $data->getImmatriculationDate());
        $this->assertSame(null, $data->getExmatriculationStatus());
        $this->assertSame(null, $data->getExmatriculationDate());
        $this->assertSame('Ing.', $data->getAcademicTitlePreceding());
        $this->assertSame('Bsc', $data->getAcademicTitleFollowing());
        $this->assertSame('Normalverbraucher', $data->getFormerFamilyName());
        $this->assertSame('1223010170', $data->getSocialSecurityNumber());
        $this->assertSame('Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=', $data->getSectorSpecificPersonalIdentifier());
        $this->assertSame('max.mustermann.temp@example.com', $data->getEmailAddressTemporary());
    }

    public function testGetPersonDataMinimal()
    {
        $RESPONSE = '
{
    "type": "resources",
    "link": [],
    "resource": [
        {
            "link": [],
            "content": {
                "type": "model-CO_LOC_DS.API_DMS_STUD_PERS_MV",
                "API_DMS_STUD_PERS_MV": {
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
                    "HOMEADDRESSNOTE": null,
                    "HOMEADDRESSSTREET": null,
                    "HOMEADDRESSPLACE": null,
                    "HOMEADDRESSPOSTCODE": null,
                    "HOMEADDRESSCOUNTRY": null,
                    "STUDADDRESSNOTE": null,
                    "STUDADDRESSSTREET": null,
                    "STUDADDRESSPLACE": null,
                    "STUDADDRESSPOSTCODE": null,
                    "STUDADDRESSCOUNTRY": null,
                    "EMAILADDRESSTU": null,
                    "EMAILADDRESSCONFIRMED": null,
                    "EMAILADDRESSTEMPORARY": null,
                    "PERSTUSTATUS": "Voranmeldung",
                    "STUDTUSTATUS": "E",
                    "IMMATRICULATIONDATE": {
                        "value": "2010-12-24"
                    },
                    "EXMATRICULATIONSTATUS": null,
                    "EXMATRICULATIONDATE": {
                        "value": null
                    },
                    "ACADEMICTITLEPRECEDING": null,
                    "ACADEMICTITLEFOLLOWING": null,
                    "FORMERFAMILYNAME": null,
                    "SOCIALSECURITYNR": null,
                    "BPK": null,
                    "GENDER": "X"
                }
            }
        }
    ]
}
        ';

        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ]);

        $data = $this->api->getPersonData('F06BCC80D6FC0BDE575B16FB2E3790D5');

        // These seem to be always present (based on manual testing only)
        $this->assertSame('1970-01-01', $data->getBirthDate());
        $this->assertSame(Gender::NonBinary, $data->getGender());
        $this->assertSame(11, $data->getNationality()->value);
        $this->assertSame('Ägypten', $data->getNationalityString());
        $this->assertSame(123123, $data->getStudentPersonNumber());
        $this->assertSame('Max', $data->getGivenName());
        $this->assertSame('Mustermann', $data->getFamilyName());
        $this->assertSame('F06BCC80D6FC0BDE575B16FB2E3790D5', $data->getIdentNumberObfuscated());
        $this->assertSame('02', $data->getAdmissionQualificationTypeNumber());
        $this->assertSame('Humanistisches Gymnasium', $data->getAdmissionQualificationType());
        $this->assertSame(PersonalStatus::PreRegistration, $data->getPersonalStatus());
        $this->assertSame(StudentStatus::NotAdmitted, $data->getStudentStatus());
        $this->assertSame('2010-12-24', $data->getImmatriculationDate());

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
        $this->assertSame(null, $data->getStudentAddressNote());
        $this->assertSame(null, $data->getStudentAddressStreet());
        $this->assertSame(null, $data->getStudentAddressPlace());
        $this->assertSame(null, $data->getStudentAddressPostCode());
        $this->assertSame(null, $data->getStudentAddressCountry());
        $this->assertSame(null, $data->getEmailAddressUniversity());
        $this->assertSame(null, $data->getEmailAddressConfirmed());
        $this->assertSame(null, $data->getEmailAddressConfirmed());
        $this->assertSame(null, $data->getExmatriculationStatus());
        $this->assertSame(null, $data->getExmatriculationDate());
        $this->assertSame(null, $data->getAcademicTitlePreceding());
        $this->assertSame(null, $data->getAcademicTitleFollowing());
        $this->assertSame(null, $data->getFormerFamilyName());
        $this->assertSame(null, $data->getSocialSecurityNumber());
        $this->assertSame(null, $data->getSectorSpecificPersonalIdentifier());
        $this->assertSame(null, $data->getEmailAddressTemporary());
    }
}
