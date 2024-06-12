<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Command;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\SyncApi;
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

        $api = new SyncApi($config);
        if ($this->clientHandler !== null) {
            $api->setClientHandler($this->clientHandler, $this->token);
        }

        $studentsApi = $api->getStudentsApi();

        $student = $studentsApi->getStudentForObfuscatedId($obfuscatedId);
        if ($student === null && is_numeric($obfuscatedId)) {
            $student = $studentsApi->getStudentForPersonNumber((int) $obfuscatedId);
        }
        if ($student === null) {
            $io->getErrorStyle()->error('student data not found');

            return Command::FAILURE;
        }

        $nr = $student->getStudentPersonNumber();

        $studiesApi = $api->getStudiesApi();
        $studies = $studiesApi->getStudies($nr);

        $studiesData = [];
        foreach ($studies as $study) {
            $entry = [
                'id' => $study->getStudyNumber(),
                'key' => $study->getStudyKey(),
                'name' => $study->getStudyName(),
                'type' => $study->getStudyType(),
                'curriculumVersion' => $study->getStudyCurriculumVersion(),
            ];
            $studiesData[] = $entry;
        }

        $applicationsApi = $api->getApplicationsApi();

        $applications = $applicationsApi->getApplications($nr);

        $applicationsData = [];
        foreach ($applications as $application) {
            $entry = [
                'id' => $application->getApplicationNumber(),
                'studyId' => $application->getStudyNumber(),
                'studentPersonNumber' => $application->getStudentPersonNumber(),
                'applicationNumber' => $application->getApplicationNumber(),
                'studyKey' => $application->getStudyKey(),
                'studyName' => $application->getStudyName(),
                'studyType' => $application->getStudyType(),
                'startSemester' => $application->getStartSemester(),
                'qualificationCertificateDate' => $application->getQualificationCertificateDate(),
                'qualificationIssuingCountry' => $application->getQualificationIssuingCountry()->forJson(),
                'qualificationType' => $application->getQualification()->forJson(),
            ];
            $applicationsData[] = $entry;
        }

        $exmatriculationStatus = $student->getExmatriculationStatus();

        // This is just a test and WIP
        $data = [
            'id' => $student->getIdentNumberObfuscated(),
            'givenName' => $student->getGivenName(),
            'familyName' => $student->getFamilyName(),
            'studentPersonNumber' => (string) $student->getStudentPersonNumber(),
            'gender' => $student->getGender()->forJson(),
            'studentStatus' => $student->getStudentStatus()->forJson(),
            'studies' => $studiesData,
            'applications' => $applicationsData,
            'nationality' => $student->getNationality()->forJson(),
            'exmatriculationStatus' => $exmatriculationStatus !== null ? $exmatriculationStatus->forJson() : null,
        ];

        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $output->writeln($json);

        return Command::SUCCESS;
    }
}
