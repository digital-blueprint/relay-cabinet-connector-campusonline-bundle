<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\JsonConverter;
use PHPUnit\Framework\TestCase;

class JsonConverterTest extends TestCase
{
    private const STUDENT_DATA = [
        'SOURCE' => 'LiveSync[Single|Normal]',
        'TIMESTAMP' => '13.06.2024T14:00:34',
        'STPERSONNR' => 123123,
        'STUDID' => '00712345',
        'GIVENNAME' => 'Max',
        'FAMILYNAME' => 'Mustermann',
        'BIRTHDATE' => [
            'value' => '1970-01-01',
        ],
        'IDENTNROBFUSCATED' => 'F06BCC80D6FC0BDE575B16FB2E3790D5',
        'NATIONALITYNR' => 11,
        'NATIONALITY' => 'Ägypten',
        'NATIONALITYSECONDARYNR' => 168,
        'NATIONALITYSECONDARY' => 'Österreich',
        'ADMISSIONQUALIFICATIONTYPENR' => '02',
        'ADMISSIONQUALIFICATIONTYPE' => 'Humanistisches Gymnasium',
        'SCHOOLCERTIFICATEDATE' => [
            'value' => '2010-12-24',
        ],
        'ADMISSIONQUALIFICATIONSTATENR' => 40,
        'ADMISSIONQUALIFICATIONSTATE' => 'Bosnien und Herzegowina',
        'TELEPHONE' => '067612345677',
        'HOMEADDRESSNOTE' => 'c/o Erika Mustermann',
        'HOMEADDRESSSTREET' => 'Hauptstraße 34',
        'HOMEADDRESSPLACE' => 'Altenmarkt bei Sankt Gallen',
        'HOMEADDRESSPOSTCODE' => '8934',
        'HOMEADDRESSCOUNTRYNR' => 11,
        'HOMEADDRESSCOUNTRY' => 'Ägypten',
        'HOMEADDRESSTELEPHONE' => '067612345678',
        'STUDADDRESSNOTE' => 'c/o Erika Mustermann',
        'STUDADDRESSSTREET' => 'Hauptstraße 42',
        'STUDADDRESSPLACE' => 'Waizenkirchen',
        'STUDADDRESSPOSTCODE' => '4730',
        'STUDADDRESSCOUNTRYNR' => 11,
        'STUDADDRESSCOUNTRY' => 'Ägypten',
        'STUDADDRESSTELEPHONE' => '067612345676',
        'EMAILADDRESSTU' => 'max.mustermann@student.tugraz.at',
        'EMAILADDRESSCONFIRMED' => 'max.mustermann@example.com',
        'EMAILADDRESSTEMPORARY' => 'max.mustermann.temp@example.com',
        'PERSSTATUS' => 'Voranmeldung',
        'STUDSTATUSKEY' => 'E',
        'STUDSTATUS' => 'nicht zugelassen',
        'TUITIONSTATUS' => 'Ausländer gleichgestellt',
        'TUITIONEXEMPTIONTYPE' => 'L Lehrgang',
        'IMMATRICULATIONDATE' => [
            'value' => '2010-12-24',
        ],
        'IMMATRICULATIONSEMESTER' => '21W',
        'EXMATRICULATIONSTATUSKEY' => 'EZ',
        'EXMATRICULATIONSTATUS' => 'ex lege',
        'EXMATRICULATIONDATE' => [
            'value' => '2023-10-31',
        ],
        'TERMSTART' => '23W',
        'TERMEND' => '24S',
        'ACADEMICTITLEPRECEDING' => 'Ing.',
        'ACADEMICTITLEFOLLOWING' => 'Bsc',
        'FORMERFAMILYNAME' => 'Normalverbraucher',
        'SOCIALSECURITYNR' => '1223010170',
        'BPK' => 'Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=',
        'GENDERKEY' => 'M',
        'GENDER' => 'Männlich',
        'NOTE' => 'some note',
    ];

    private const EXPECTED = [
        'id' => 'F06BCC80D6FC0BDE575B16FB2E3790D5',
        'webUrl' => 'https://dummy.at/dummy/wbStEvidenz.StEvi?pStPersonNr=123123',
        'syncDateTime' => '2024-06-13T13:00:34+00:00',
        'studentId' => '00712345',
        'givenName' => 'Max',
        'familyName' => 'Mustermann',
        'birthDate' => '1970-01-01',
        'studentPersonNumber' => '123123',
        'studies' => [
        ],
        'applications' => [
        ],
        'nationality' => [
            'key' => '11',
            'translations' => [
                'de' => 'Ägypten',
                'en' => 'Egypt',
            ],
        ],
        'nationalitySecondary' => [
            'key' => '168',
            'translations' => [
                'de' => 'Österreich',
                'en' => 'Austria',
            ],
        ],
        'admissionQualificationType' => [
            'key' => '02',
            'translations' => [
                'de' => 'Humanistisches Gymnasium',
                'en' => 'Humanistisches Gymnasium',
            ],
        ],
        'admissionQualificationState' => [
            'key' => '40',
            'translations' => [
                'de' => 'Bosnien und Herzegowina',
                'en' => 'Bosnia & Herzegovina',
            ],
        ],
        'telephoneNumber' => '067612345677',
        'schoolCertificateDate' => '2010-12-24',
        'homeAddressNote' => 'c/o Erika Mustermann',
        'homeAddressStreet' => 'Hauptstraße 34',
        'homeAddressPlace' => 'Altenmarkt bei Sankt Gallen',
        'homeAddressPostCode' => '8934',
        'homeAddressCountry' => [
            'key' => '11',
            'translations' => [
                'de' => 'Ägypten',
                'en' => 'Egypt',
            ],
        ],
        'homeAddressTelephoneNumber' => '067612345678',
        'studentAddressNote' => 'c/o Erika Mustermann',
        'studentAddressStreet' => 'Hauptstraße 42',
        'studentAddressPlace' => 'Waizenkirchen',
        'studentAddressPostCode' => '4730',
        'studentAddressCountry' => [
            'key' => '11',
            'translations' => [
                'de' => 'Ägypten',
                'en' => 'Egypt',
            ],
        ],
        'studentAddressTelephoneNumber' => '067612345676',
        'emailAddressUniversity' => 'max.mustermann@student.tugraz.at',
        'emailAddressConfirmed' => 'max.mustermann@example.com',
        'emailAddressTemporary' => 'max.mustermann.temp@example.com',
        'personalStatus' => [
            'key' => 'Voranmeldung',
            'translations' => [
                'de' => 'Voranmeldung',
                'en' => 'pre-registration',
            ],
        ],
        'studentStatus' => [
            'key' => 'E',
            'translations' => [
                'de' => 'nicht zugelassen',
                'en' => 'not admitted',
            ],
        ],
        'tuitionStatus' => 'Ausländer gleichgestellt',
        'tuitionExemptionType' => 'L Lehrgang',
        'immatriculationDate' => '2010-12-24',
        'exmatriculationStatus' => [
            'key' => 'EZ',
            'translations' => [
                'de' => 'ex lege (EZ)',
                'en' => 'ex lege (EZ)',
            ],
        ],
        'immatriculationSemester' => '21W',
        'exmatriculationDate' => '2023-10-31',
        'termStart' => '23W',
        'termEnd' => '24S',
        'academicTitlePreceding' => 'Ing.',
        'academicTitleFollowing' => 'Bsc',
        'formerFamilyName' => 'Normalverbraucher',
        'socialSecurityNumber' => '1223010170',
        'sectorSpecificPersonalIdentifier' => 'Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=',
        'gender' => [
            'key' => 'M',
            'translations' => [
                'de' => 'Männlich',
                'en' => 'male',
            ],
        ],
        'note' => 'some note',
    ];

    public function testConvert()
    {
        $baseApi = new BaseApi(new Connection('https://dummy.at/dummy', 'foo', 'bar'), 'bla', new \DateTimeZone('Europe/London'));
        $student = new Student(self::STUDENT_DATA, $baseApi);
        $res = JsonConverter::convertToJsonObject($student, [], []);
        $this->assertSame(self::EXPECTED, $res);
    }
}
