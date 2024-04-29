<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Command;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi\PersonDataApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowPersonDataCommand extends Command
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
        $this->setName('dbp:relay:cabinet-connector-campusonline:show-person-data');
        $this->setDescription('Show person data for an obfuscated ID');
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

        $table = new Table($output);
        $table->setHeaders(['Key', 'Value']);
        $data = $personData->data;
        ksort($data);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $value['value'];
            }
            $table->addRow([$key, $value]);
        }
        $table->render();

        return Command::SUCCESS;
    }
}
