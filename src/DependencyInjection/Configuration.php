<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dbp_relay_cabinet_connector_campusonline');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('api_url')
                        ->isRequired()
                        ->info('The base URL of the CO instance')
                        ->example('https://online.mycampus.org/campus_online')
                    ->end()
                    ->scalarNode('client_id')
                        ->isRequired()
                        ->info('The OAuth2 client ID')
                        ->example('my-client')
                    ->end()
                    ->scalarNode('client_secret')
                        ->isRequired()
                        ->info('The OAuth2 client secret')
                        ->example('my-secret')
                    ->end()
                    ->scalarNode('data_service_name_person_data')
                        ->isRequired()
                        ->info('The data service name for the PersonData CO API')
                        ->example('loc_apiDmsStudPersMv')
                    ->end()
                    ->scalarNode('data_service_name_active_studies')
                        ->isRequired()
                        ->info('The data service name for the ActiveStudies CO API')
                        ->example('loc_apiDmsStudien')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
