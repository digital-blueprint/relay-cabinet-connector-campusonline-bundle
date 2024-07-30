<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\Application;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;
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

    private const STUDY_DATA = [
        'SOURCE' => 'LiveSync[Single|Normal]',
        'TIMESTAMP' => '13.06.2024T13:08:15',
        'STSTUDIUMNR' => 253324,
        'STPERSONNR' => 123456,
        'STUDYKEY' => 'UF 066 921',
        'STUDYTYPE' => 'Masterstudium',
        'STUDYNAME' => 'Masterstudium; Computer Science',
        'STUDYSEMESTER' => 6,
        'STUDYSTATUSKEY' => 'I',
        'STUDYSTATUS' => 'gemeldet',
        'STUDYCURRICULUMVERSION' => '2022W',
        'STUDYIMMATRICULATIONDATE' => [
            'value' => '2021-01-01',
        ],
        'STUDYIMMATRICULATIONSEMESTER' => '20S',
        'STUDYEXMATRICULATIONDATE' => [
            'value' => '2022-01-01',
        ],
        'STUDYEXMATRICULATIONSEMESTER' => '24S',
        'STUDYEXMATRICULATIONTYPEKEY' => 'EZ',
        'STUDYEXMATRICULATIONTYPE' => 'auf Antrag',
        'STUDYQUALIFICATIONTYPENR' => '41',
        'STUDYQUALIFICATIONTYPE' => 'Master-/Diplomst.eigene Univ.',
        'STUDYQUALIFICATIONDATE' => [
            'value' => '2010-01-01',
        ],
        'STUDYQUALIFICATIONSTATENR' => 168,
        'STUDYQUALIFICATIONSTATE' => 'Österreich',
        'ADDITIONALCERTIFICATE' => 'EDG | EGR',
    ];

    private const APPLICATION_DATA = [
        'SOURCE' => 'LiveSync',
        'TIMESTAMP' => '13.06.2024T12:01:15',
        'BEWERBUNGNR' => 12345,
        'STPERSONNR' => 123456,
        'STSTUDIUMNR' => null,
        'APPLICANTSTARTOFSTUDY' => '21W',
        'APPLICANTSTUDYTYPE' => 'Bachelorstudium',
        'APPLICANTSTUDYNAME' => 'Bachelorstudium; Physik',
        'APPLICANTSTUDYKEY' => 'UF 033 678',
        'APPLICANTQUALIFICATIONTYPE' => '25 - ausländische Reifeprüfung',
        'APPLICANTQUALIFICATIONDATE' => [
            'value' => '1970-01-01',
        ],
        'APPLICANTQUALIFICATIONSTATENR' => 40,
        'APPLICANTQUALIFICATIONSTATE' => 'Bosnien und Herzegowina',
    ];

    private const EXPECTED = [
        'id' => 'F06BCC80D6FC0BDE575B16FB2E3790D5',
        'webUrl' => 'https://dummy.at/dummy/wbStEvidenz.StEvi?pStPersonNr=123123',
        'syncDateTime' => '2024-06-13T11:01:15+00:00',
        'studentId' => '00712345',
        'givenName' => 'Max',
        'familyName' => 'Mustermann',
        'birthDate' => '1970-01-01',
        'studentPersonNumber' => '123123',
        'studies' => [
            0 => [
                'id' => 253324,
                'webUrl' => 'https://dummy.at/dummy/wbStmStudiendaten.wbStudiendetails?pStPersonNr=123456&pStStudiumNr=253324',
                'studentPersonNumber' => 123456,
                'key' => 'UF 066 921',
                'type' => 'Masterstudium',
                'name' => 'Masterstudium; Computer Science',
                'semester' => 6,
                'status' => [
                    'key' => 'I',
                    'translations' => [
                        'de' => 'gemeldet',
                        'en' => 'gemeldet',
                    ],
                ],
                'curriculumVersion' => '2022W',
                'immatriculationDate' => '2021-01-01',
                'immatriculationSemester' => '20S',
                'exmatriculationDate' => '2022-01-01',
                'exmatriculationSemester' => '24S',
                'qualificationType' => [
                    'key' => '41',
                    'translations' => [
                        'de' => 'Master-/Diplomst.eigene Univ.',
                        'en' => 'Master/ Diploma study programme at own university',
                    ],
                ],
                'qualificationDate' => '2010-01-01',
                'qualificationState' => [
                    'key' => '168',
                    'translations' => [
                        'de' => 'Österreich',
                        'en' => 'Austria',
                    ],
                ],
                'exmatriculationType' => [
                    'key' => 'EZ',
                    'translations' => [
                        'de' => 'ex lege (EZ)',
                        'en' => 'ex lege (EZ)',
                    ],
                ],
                'additionalCertificates' => [
                    0 => [
                        'key' => 'EDG',
                        'translations' => [
                            'de' => 'Erg.Prfg. - Darstellende Geometrie',
                            'en' => 'suppl.exam. - Descriptive Geometry',
                        ],
                    ],
                    1 => [
                        'key' => 'EGR',
                        'translations' => [
                            'de' => 'Erg.Prfg. - Griechisch',
                            'en' => 'suppl.exam. - Greek',
                        ],
                    ],
                ],
            ],
        ],
        'applications' => [
            0 => [
                'id' => 12345,
                'studyId' => null,
                'studentPersonNumber' => 123456,
                'studyKey' => 'UF 033 678',
                'studyName' => 'Bachelorstudium; Physik',
                'studyType' => 'Bachelorstudium',
                'startSemester' => '21W',
                'qualificationCertificateDate' => '1970-01-01',
                'qualificationIssuingCountry' => [
                    'key' => '40',
                    'translations' => [
                        'de' => 'Bosnien und Herzegowina',
                        'en' => 'Bosnia & Herzegovina',
                    ],
                ],
                'qualificationType' => [
                    'key' => '25',
                    'translations' => [
                        'de' => 'ausländische Reifeprüfung',
                        'en' => 'foreign secondary school leaving exam',
                    ],
                ],
            ],
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
        'studyAddressNote' => 'c/o Erika Mustermann',
        'studyAddressStreet' => 'Hauptstraße 42',
        'studyAddressPlace' => 'Waizenkirchen',
        'studyAddressPostCode' => '4730',
        'studyAddressCountry' => [
            'key' => '11',
            'translations' => [
                'de' => 'Ägypten',
                'en' => 'Egypt',
            ],
        ],
        'studyAddressTelephoneNumber' => '067612345676',
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
        $study = new Study(self::STUDY_DATA, $baseApi);
        $application = new Application(self::APPLICATION_DATA, $baseApi);
        $res = JsonConverter::convertToJsonObject($student, [$study], [$application]);
        $this->assertSame(self::EXPECTED, $res);
    }
}
