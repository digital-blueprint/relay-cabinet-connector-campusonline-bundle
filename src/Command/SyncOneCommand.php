<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Command;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\SyncApi;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Psr\Cache\CacheItemPoolInterface;
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

    private ?CacheItemPoolInterface $cachePool;

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

    public function setCache(?CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
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

        $item = $this->cachePool->getItem('cursor');
        $cursor = null;
        if ($item->isHit()) {
            $cursor = $item->get();
        }

        $config = $this->config;

        $api = new CoApi($config);
        $api->setLogger($this->logger);
        if ($this->clientHandler !== null) {
            $api->setClientHandler($this->clientHandler, $this->token);
        }

        if ($this->config->getCacheEnabled()) {
            // Cache everything for now to make development easier
            $cacheMiddleWare = new CacheMiddleware(
                new GreedyCacheStrategy(
                    new Psr6CacheStorage($this->cachePool),
                    $this->config->getCacheTtl()
                )
            );

            $stack = HandlerStack::create();
            $stack->push($cacheMiddleWare, 'cache');

            $api->setClientHandler($stack, null);
        }

        $sync = new SyncApi($api, $config);
        $res = $sync->getSome([$obfuscatedId], $cursor);
        if ($res->getPersons() === [] && is_numeric($obfuscatedId)) {
            $res = $sync->getSome([$obfuscatedId], $cursor, usePersonNumber: true);
        }

        if ($res->getPersons() === []) {
            $io->getErrorStyle()->error('student data not found');

            return Command::FAILURE;
        }

        $data = array_values($res->getPersons())[0];
        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $output->writeln($json);

        $item->set($res->getCursor());
        $item->expiresAfter($this->config->getCacheTtl());
        $this->cachePool->save($item);

        return Command::SUCCESS;
    }
}
