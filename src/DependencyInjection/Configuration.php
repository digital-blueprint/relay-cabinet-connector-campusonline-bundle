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
                    ->scalarNode('data_service_name_students')
                        ->isRequired()
                        ->info('The data service name for the Student CO API')
                        ->example('loc_api-dms.dmsstudents')
                    ->end()
                    ->scalarNode('data_service_name_studies')
                        ->isRequired()
                        ->info('The data service name for the Studies CO API')
                        ->example('loc_api-dms.dmsstudies')
                    ->end()
                    ->scalarNode('data_service_name_applications')
                        ->isRequired()
                        ->info('The data service name for the Applications CO API')
                        ->example('loc_api-dms.dmsapplicants')
                    ->end()
                    ->booleanNode('exclude_inactive')
                        ->defaultFalse()
                        ->info('Set to exclude inactive students and studies (useful for testing with less data)')
                        ->example('true')
                    ->end()
                    ->booleanNode('cache')
                        ->defaultFalse()
                        ->info('Enable caching for easier development')
                        ->example('true')
                    ->end()
                    ->integerNode('page_size')
                        ->defaultValue(20000)
                        ->info('The page size used for CO requests')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
