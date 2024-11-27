<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetBundle\PersonSync\PersonSyncInterface;
use Dbp\Relay\CabinetBundle\PersonSync\PersonSyncResultInterface;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class PersonSync implements PersonSyncInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    private ConfigurationService $config;
    private ?CacheItemPoolInterface $cachePool;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
        $this->logger = new NullLogger();
    }

    public function setCache(?CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    private function getSyncApi(): SyncApi
    {
        $coApi = new CoApi($this->config);

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
            $coApi->setClientHandler($stack, null);
        }

        $coApi->setLogger($this->logger);
        $syncApi = new SyncApi($coApi, $this->config);
        $syncApi->setLogger($this->logger);

        return $syncApi;
    }

    public function getAllPersons(?string $cursor = null): PersonSyncResultInterface
    {
        if ($cursor === null) {
            return $this->getSyncApi()->getAllFirstTime();
        } else {
            return $this->getSyncApi()->getAllSince($cursor);
        }
    }

    public function getPersons(array $ids, ?string $cursor = null): PersonSyncResultInterface
    {
        return $this->getSyncApi()->getSome($ids, $cursor);
    }
}
