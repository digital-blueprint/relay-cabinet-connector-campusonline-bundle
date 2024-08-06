<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Command;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\SyncApi;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncOneCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
        $this->logger = new NullLogger();
    }

    protected function configure(): void
    {
        $this->setName('dbp:relay:cabinet-connector-campusonline:sync-one');
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

        $api = new CoApi($config);
        $api->setLogger($this->logger);
        if ($this->clientHandler !== null) {
            $api->setClientHandler($this->clientHandler, $this->token);
        }

        $sync = new SyncApi($api, $config);
        $data = $sync->getSingleForObfuscatedId($obfuscatedId);
        if ($data === null && is_numeric($obfuscatedId)) {
            $data = $sync->getSingleForPersonNumber((int) $obfuscatedId);
        }

        if ($data === null) {
            $io->getErrorStyle()->error('student data not found');

            return Command::FAILURE;
        }

        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $output->writeln($json);

        return Command::SUCCESS;
    }
}
