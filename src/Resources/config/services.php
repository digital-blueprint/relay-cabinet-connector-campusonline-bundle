<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Dbp\Relay\CabinetBundle\PersonSync\PersonSyncInterface;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Command\SyncCommand;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Command\SyncOneCommand;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\PersonSync;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->load('Dbp\\Relay\\CabinetConnectorCampusonlineBundle\\Service\\', '../../Service')
        ->autowire()
        ->autoconfigure();

    $services->load('Dbp\\Relay\\CabinetConnectorCampusonlineBundle\\Command\\', '../../Command')
        ->autowire()
        ->autoconfigure();

    $services->set(SyncCommand::class)
        ->autowire()
        ->autoconfigure()
        ->call('setCache', [service('dbp.relay.cabinet_connector_campusonline.cache.sync')]);

    $services->set(SyncOneCommand::class)
        ->autowire()
        ->autoconfigure()
        ->call('setCache', [service('dbp.relay.cabinet_connector_campusonline.cache.sync')]);

    $services->set('dbp.relay.cabinet_connector_campusonline.cache.sync')
        ->parent('cache.app')
        ->tag('cache.pool', ['default_lifetime' => 60]);

    $services->set(PersonSync::class)
        ->autowire()
        ->autoconfigure()
        ->call('setCache', [service('dbp.relay.cabinet_connector_campusonline.cache.sync')]);

    $services->alias(PersonSyncInterface::class, PersonSync::class);
};
