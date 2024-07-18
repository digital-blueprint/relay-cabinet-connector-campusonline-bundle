<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CountryUtils;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ExmatriculationStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\HigherEducationEntranceQualification;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Gender;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Nationality;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\StudentStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\AdditionalExams;
use Monolog\Test\TestCase;

class ApiValueTypesTest extends TestCase
{
    public function testNationality()
    {
        $this->assertSame('Austria', (new Nationality(168))->getName('en'));
        $this->assertSame('Bajor', (new Nationality(2369, ['de' => 'Bajor']))->getName());
        $this->assertSame(168, (new Nationality(168))->value);
        $this->assertSame('AUT', (new Nationality(168))->getAlpha3Code());

        $this->assertNull((new Nationality(249))->getAlpha3Code());
        $this->assertNull((new Nationality(4242))->getAlpha3Code());
    }

    public function testCountryGetName()
    {
        $this->assertSame('Österreich', CountryUtils::getName(168, 'de'));
        $this->assertSame('Austria', CountryUtils::getName(168, 'en'));
        $this->assertSame('Autriche', CountryUtils::getName(168, 'fr'));
        $this->assertSame('Kosovo', CountryUtils::getName(257, 'de'));
    }

    public function testCountryGetNameSpecial()
    {
        $this->assertSame('Europäische Union', CountryUtils::getName(249, 'de'));
        $this->assertSame('European Union', CountryUtils::getName(249, 'en'));
        $this->assertSame('Europäische Union', CountryUtils::getName(249, 'fr'));
    }

    public function testCountryGetNameUnkown()
    {
        $this->assertSame('Unbekannter Wert (4242)', CountryUtils::getName(4242, 'de'));
        $this->assertSame('unknown value (4242)', CountryUtils::getName(4242, 'en'));
        $this->assertSame('Unbekannter Wert (4242)', CountryUtils::getName(4242, 'fr'));
    }

    public function testSchoolType()
    {
        $this->assertSame('IB Diploma (foreign country)', (new HigherEducationEntranceQualification('46'))->getName());
        $this->assertSame('IB Diploma (Ausland)', (new HigherEducationEntranceQualification('46'))->getName('de'));
        $this->assertSame('Unbekannter Wert (1234)', (new HigherEducationEntranceQualification('1234'))->getName('de'));
    }

    public function testGender()
    {
        $this->assertSame('Weiblich', (new Gender('W'))->getName('de'));
        $this->assertSame('Weiblich', (new Gender('W'))->getName('fr'));
        $this->assertSame('unknown value (P)', (new Gender('P'))->getName('en'));
        $this->assertSame('Unbekannter Wert (P)', (new Gender('P'))->getName('fr'));
        $this->assertSame('Hello', (new Gender('H', ['de' => 'Hello']))->getName('fr'));
    }

    public function testStudentStatus()
    {
        $this->assertSame('Außerordentlich', (new StudentStatus('A'))->getName('de'));
        $this->assertSame('Außerordentlich', (new StudentStatus('A'))->getName('fr'));
        $this->assertSame('unknown value (P)', (new StudentStatus('P'))->getName('en'));
        $this->assertSame('Unbekannter Wert (P)', (new StudentStatus('P'))->getName('fr'));
    }

    public function testExmatriculationStatus()
    {
        $this->assertSame('auf Antrag (A)', (new ExmatriculationStatus('A'))->getName('de'));
        $this->assertSame('auf Antrag (A)', (new ExmatriculationStatus('A'))->getName('fr'));
        $this->assertSame('foo (P)', (new ExmatriculationStatus('P', ['de' => 'foo (P)']))->getName('fr'));
        $this->assertSame('unknown value (P)', (new ExmatriculationStatus('P'))->getName('en'));
        $this->assertSame('Unbekannter Wert (P)', (new ExmatriculationStatus('P'))->getName('fr'));
    }

    public function testsAdditionalExams()
    {
        $this->assertSame([], (new AdditionalExams(null))->items);
        $this->assertSame('suppl.exam. - Descriptive Geometry', (new AdditionalExams('EDG'))->items[0]->getName());
        $this->assertSame('unknown value (FOO)', (new AdditionalExams('FOO'))->items[0]->getName());
        $this->assertSame('unknown value (FOO)', (new AdditionalExams('FOO | EDG'))->items[0]->getName());
        $this->assertSame('suppl.exam. - Descriptive Geometry', (new AdditionalExams('FOO | EDG'))->items[1]->getName());
    }
}
