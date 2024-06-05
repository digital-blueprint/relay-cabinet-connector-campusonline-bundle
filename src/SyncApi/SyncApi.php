<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\CampusonlineApi\Helpers\ApiException;
use Dbp\CampusonlineApi\Rest\Connection;
use Dbp\CampusonlineApi\Rest\Generic\ApiResource;
use GuzzleHttp\Exception\GuzzleException;
use League\Uri\UriTemplate;
use Psr\Http\Message\ResponseInterface;

class SyncApi
{
    private const VALID_IDs = ['StPersonNr', 'IdentNrObfuscated', 'StStudiumNr'];

    private string $dataService;

    private Connection $connection;

    public function __construct(Connection $connection, string $dataService)
    {
        $this->connection = $connection;
        $this->dataService = $dataService;
    }

    public static function create(string $baseUrl, string $dataService, string $clientId, string $clientSecret)
    {
        $connection = new Connection($baseUrl, $clientId, $clientSecret);

        return new self($connection, $dataService);
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getResource(string $field, string $value): ?ApiResource
    {
        $collection = $this->getResourceCollection(page: 1, pageSize: 2, filters: [$field => $value]);
        if (count($collection) === 1) {
            return $collection[0];
        } elseif (count($collection) >= 1) {
            throw new ApiException("Filter on '$field' with '$value' returned multiple results while only <=1 is allowed");
        }

        return null;
    }

    public function getResourceCollection(?string $lastSyncDate = null, bool $syncOnlyInactive = false, ?int $page = null, ?int $pageSize = null, array $filters = []): array
    {
        $connection = $this->connection;
        $dataService = $connection->getDataServiceId($this->dataService);

        // We can only use one of the allowed ones
        if (count($filters) > 1 || (count($filters) === 1 && !in_array(array_keys($filters)[0], self::VALID_IDs, true))) {
            throw new \RuntimeException('invalid filter');
        }

        $params = $filters;
        if ($page !== null) {
            $params['PageNr'] = $page;
        }
        if ($pageSize !== null) {
            $params['PageSize'] = $pageSize;
        }
        if ($lastSyncDate !== null) {
            $params['LastSyncDate'] = $lastSyncDate;
        }
        if ($syncOnlyInactive === true) {
            $params['SyncOnlyInactive'] = 'YES';
        }

        $uriTemplate = new UriTemplate('pl/rest/{service}/{?%24format}{&params*}');
        $vars = [
            'service' => $dataService,
            '%24format' => 'json',
            'params' => $params,
        ];
        $uri = (string) $uriTemplate->expand($vars);

        $client = $connection->getClient();
        try {
            $response = $client->get($uri);
        } catch (GuzzleException $guzzleException) {
            throw ApiException::fromGuzzleException($guzzleException);
        }

        return $this->parseResourceList($response);
    }

    /**
     * @return ApiResource[]
     */
    private function parseResourceList(ResponseInterface $response): array
    {
        $content = (string) $response->getBody();

        try {
            $json = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new ApiException('json response invalid');
        }

        $resultList = [];
        foreach ($json['resource'] as $resource) {
            $content = $resource['content'];
            $type = $content['type'];

            $result = new ApiResource();
            $result->type = $type;

            $realContent = null;
            foreach ($content as $key => $value) {
                if ($key !== 'type' && is_array($value)) {
                    $realContent = $value;
                    break;
                }
            }
            if ($realContent === null) {
                throw new \RuntimeException('content missing');
            }
            $result->content = $realContent;
            $resultList[] = $result;
        }

        return $resultList;
    }
}
