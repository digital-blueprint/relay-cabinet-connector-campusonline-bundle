<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CountryUtils;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\HigherEducationEntranceQualification;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Gender;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Nationality;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\StudentStatus;
use Monolog\Test\TestCase;

class ApiValueTypesTest extends TestCase
{
    public function testNationality()
    {
        $this->assertSame('Austria', (new Nationality(168))->getName('en'));
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
        $this->assertSame('European Union', CountryUtils::getName(249, 'fr'));
    }

    public function testCountryGetNameUnkown()
    {
        $this->assertSame('Unbekannt (4242)', CountryUtils::getName(4242, 'de'));
        $this->assertSame('Unknown (4242)', CountryUtils::getName(4242, 'en'));
        $this->assertSame('Unknown (4242)', CountryUtils::getName(4242, 'fr'));
    }

    public function testSchoolType()
    {
        $this->assertSame('IB Diploma (foreign country)', HigherEducationEntranceQualification::fromId('46')->getName());
        $this->assertSame('IB Diploma (Ausland)', HigherEducationEntranceQualification::fromId('46')->getName('de'));
        $this->assertSame('Unbekannt (1234)', HigherEducationEntranceQualification::fromId('1234')->getName('de'));
    }

    public function testGender()
    {
        $this->assertSame('Weiblich', (new Gender('W'))->getName('de'));
        $this->assertSame('Weiblich', (new Gender('W'))->getName('fr'));
        $this->assertSame('unknown value (P)', (new Gender('P'))->getName('en'));
        $this->assertSame('Unbekannter Wert (P)', (new Gender('P'))->getName('fr'));
    }

    public function testStudentStatus()
    {
        $this->assertSame('Außerordentlich', (new StudentStatus('A'))->getName('de'));
        $this->assertSame('Außerordentlich', (new StudentStatus('A'))->getName('fr'));
        $this->assertSame('unknown value (P)', (new StudentStatus('P'))->getName('en'));
        $this->assertSame('Unbekannter Wert (P)', (new StudentStatus('P'))->getName('fr'));
    }
}
