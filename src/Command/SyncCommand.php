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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncCommand extends Command implements LoggerAwareInterface
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
        $this->setName('dbp:relay:cabinet-connector-campusonline:sync');
        $this->setDescription('Run a sync');
        $this->addOption('--full', mode: InputOption::VALUE_NONE);
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
        $full = $input->getOption('full');

        $item = $this->cachePool->getItem('cursor');
        $cursor = null;
        if ($item->isHit() && !$full) {
            $cursor = $item->get();
        }

        $config = $this->config;

        $api = new CoApi($config);
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

        $api->setLogger($this->logger);
        $sync = new SyncApi($api, $config);
        $sync->setLogger($this->logger);

        if ($cursor === null) {
            $res = $sync->getAll();
        } else {
            $res = $sync->getAllSince($cursor);
        }

        $io->info('Synced '.count($res->getPersons()).' persons. Full sync: '.($res->isFullSyncResult() ? 'yes' : 'no'));

        $item->set($res->getCursor());
        $item->expiresAfter($this->config->getCacheTtl());
        $this->cachePool->save($item);

        return Command::SUCCESS;
    }
}
