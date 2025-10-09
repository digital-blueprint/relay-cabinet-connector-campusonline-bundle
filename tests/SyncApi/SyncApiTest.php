<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Tests\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi\SyncApi;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class SyncApiTest extends TestCase
{
    private CoApi $api;

    private const RESPONSE_EMPTY = '
{
    "type": "resources",
    "link": [],
    "resource": []
}
        ';
    private ConfigurationService $config;

    public function setUp(): void
    {
        parent::setUp();
        $config = new ConfigurationService();
        $config->setConfig([
            'api_url' => 'https://dummy.at/dummy',
            'api_time_zone' => 'Europe/Vienna',
            'client_id' => '',
            'client_secret' => '',
            'data_service_name_students' => '',
            'page_size' => 123,
            'internal' => [
                'exclude_inactive' => false,
                'incremental_sync_threshold' => 200,
            ],
        ]);
        $this->config = $config;
        $this->api = new CoApi($config);
        $this->mockResponses([]);
    }

    private function mockResponses(array $responses)
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->api->setClientHandler($stack, 'nope');
    }

    public function testGetSome()
    {
        $api = new SyncApi($this->api, $this->config);
        $this->mockResponses([
            new Response(200, ['Content-Type' => 'application/json'], self::RESPONSE_EMPTY),
        ]);
        $res = $api->getSome(['foo']);
        $this->assertSame([], $res->getPersons());
        $this->assertIsString($res->getCursor());
    }
}
