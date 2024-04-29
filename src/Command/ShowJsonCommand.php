<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Command;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\ActiveStudiesApi\ActiveStudiesApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi\PersonDataApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowJsonCommand extends Command
{
    private ConfigurationService $config;

    /**
     * @var callable|null
     */
    private $clientHandler;
    private ?string $token;

    public function __construct(ConfigurationService $config)
    {
        parent::__construct();
        $this->config = $config;
        $this->clientHandler = null;
        $this->token = null;
    }

    protected function configure(): void
    {
        $this->setName('dbp:relay:cabinet-connector-campusonline:show-json');
        $this->setDescription('Show JSON for an obfuscated ID');
        $this->addArgument('obfuscated-id', InputArgument::REQUIRED, 'obfuscated id');
    }

    public function setClientHandler(?callable $handler, string $token): void
    {
        $this->clientHandler = $handler;
        $this->token = $token;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $obfuscatedId = $input->getArgument('obfuscated-id');
        $config = $this->config;

        $personDataApi = new PersonDataApi($config);

        if ($this->clientHandler !== null) {
            $connection = $personDataApi->getApi()->getConnection();
            $connection->setClientHandler($this->clientHandler);
            $connection->setToken($this->token);
        }

        $personData = $personDataApi->getPersonData($obfuscatedId);
        if ($personData === null) {
            $io->getErrorStyle()->error('person data not found');

            return Command::FAILURE;
        }

        $nr = $personData->getStudentPersonNumber();
        $activeStudiesApi = new ActiveStudiesApi($config);
        if ($this->clientHandler !== null) {
            $connection = $activeStudiesApi->getApi()->getConnection();
            $connection->setClientHandler($this->clientHandler);
            $connection->setToken($this->token);
        }

        $studies = $activeStudiesApi->getActiveStudies($nr);

        $studiesData = [];
        foreach ($studies as $study) {
            $entry = [
                'key' => $study->getStudyKey(),
                'name' => $study->getStudyName(),
                'type' => $study->getStudyType(),
                'curriculumVersion' => $study->getStudyCurriculumVersion(),
            ];
            $studiesData[] = $entry;
        }

        // This is just a test and WIP
        $data = [
            'id' => $obfuscatedId,
            'givenName' => $personData->getGivenName(),
            'familyName' => $personData->getFamilyName(),
            'studentPersonNumber' => (string) $personData->getStudentPersonNumber(),
            'gender' => [
                'key' => $personData->getGender()->value,
                'translations' => [
                    'de' => $personData->getGender()->getName('de'),
                    'en' => $personData->getGender()->getName('en'),
                ],
            ],
            'studentStatus' => [
                'key' => $personData->getStudentStatus()->value,
                'translations' => [
                    'de' => $personData->getStudentStatus()->getName('de'),
                    'en' => $personData->getStudentStatus()->getName('en'),
                ],
            ],
            'activeStudies' => $studiesData,
            'nationality' => [
                'key' => (string) $personData->getNationality()->value,
                'translations' => [
                    'de' => $personData->getNationality()->getName('de'),
                    'en' => $personData->getNationality()->getName('en'),
                ],
            ],
        ];

        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $output->writeln($json);

        return Command::SUCCESS;
    }
}
