<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\Command\ShowStudentCommand;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTest extends KernelTestCase
{
    private ShowStudentCommand $command;

    public function setUp(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('dbp:relay:cabinet-connector-campusonline:show-student');
        assert($command instanceof ShowStudentCommand);
        $this->command = $command;
    }

    public function testListThings()
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
                    "HOMEADDRESSCOUNTRYNR": null,
                    "HOMEADDRESSCOUNTRY": null,
                    "HOMEADDRESSTELEPHONE": null,
                    "STUDADDRESSNOTE": null,
                    "STUDADDRESSSTREET": null,
                    "STUDADDRESSPLACE": null,
                    "STUDADDRESSPOSTCODE": null,
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
                    "IMMATRICULATIONSEMESTER": "21W",
                    "EXMATRICULATIONSTATUSKEY": null,
                    "EXMATRICULATIONSTATUS": null,
                    "EXMATRICULATIONDATE": {
                        "value": null
                    },
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

        $responses = [
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ];

        $mockHandler = new MockHandler($responses);
        $clientHandler = HandlerStack::create($mockHandler);
        $this->command->setClientHandler($clientHandler, 'foobar');
        $commandTester = new CommandTester($this->command);
        $res = $commandTester->execute(['obfuscated-id' => 'F06BCC80D6FC0BDE575B16FB2E3790D5']);
        $this->assertStringContainsString('Mustermann', $commandTester->getDisplay());
        $this->assertSame(0, $res);
    }

    public function testNotFound(): void
    {
        $RESPONSE = '
{
    "type": "resources",
    "link": [],
    "resource": []
}
        ';

        $responses = [
            new Response(200, ['Content-Type' => 'application/json'], $RESPONSE),
        ];

        $mockHandler = new MockHandler($responses);
        $clientHandler = HandlerStack::create($mockHandler);
        $this->command->setClientHandler($clientHandler, 'foobar');
        $commandTester = new CommandTester($this->command);
        $res = $commandTester->execute(['obfuscated-id' => 'F06BCC80D6FC0BDE575B16FB2E3790D5']);
        $this->assertSame(1, $res);
    }
}
