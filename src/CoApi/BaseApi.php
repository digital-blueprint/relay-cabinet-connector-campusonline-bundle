<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

use GuzzleHttp\Exception\GuzzleException;
use League\Uri\UriTemplate;
use Psr\Http\Message\ResponseInterface;

class BaseApi
{
    private const VALID_IDs = ['StPersonNr', 'IdentNrObfuscated', 'StStudiumNr'];

    private string $dataService;

    private Connection $connection;
    private \DateTimeZone $dateTimeZone;

    public function __construct(Connection $connection, string $dataService, \DateTimeZone $dateTimeZone)
    {
        $this->connection = $connection;
        $this->dataService = $dataService;
        $this->dateTimeZone = $dateTimeZone;
    }

    public function getBaseUrl(): string
    {
        return $this->connection->getBaseUrl();
    }

    public function getResource(string $field, string $value): ?BaseResource
    {
        $collection = $this->getResourceCollection(page: 1, pageSize: 2, filters: [$field => $value]);
        if (count($collection) === 1) {
            return $collection[0];
        } elseif (count($collection) >= 1) {
            throw new ApiException("Filter on '$field' with '$value' returned multiple results while only <=1 is allowed");
        }

        return null;
    }

    public function getResourceCollection(?\DateTimeInterface $lastSyncDate = null, bool $syncOnlyInactive = false, ?int $page = null, ?int $pageSize = null, array $filters = []): array
    {
        $connection = $this->connection;

        // We can only use one of the allowed ones
        if (count($filters) > 1 || (count($filters) === 1 && !in_array(array_keys($filters)[0], self::VALID_IDs, true))) {
            throw new \RuntimeException('invalid filter');
        }

        if ($syncOnlyInactive && $lastSyncDate !== null) {
            throw new \RuntimeException('lastSyncDate only works for active resources');
        }

        $params = $filters;
        if ($page !== null) {
            $params['PageNr'] = $page;
        }
        if ($pageSize !== null) {
            $params['PageSize'] = $pageSize;
        }
        if ($lastSyncDate !== null) {
            $params['LastSyncDate'] = Utils::dateTimeToSyncTimestamp($lastSyncDate, $this->dateTimeZone);
        }
        if ($syncOnlyInactive === true) {
            $params['SyncOnlyInactive'] = 'YES';
        }

        $uriTemplate = new UriTemplate('pl/rest/{service}/{?%24format}{&params*}');
        $vars = [
            'service' => $this->dataService,
            '%24format' => 'json',
            'params' => $params,
        ];
        $uri = (string) $uriTemplate->expand($vars);

        $client = $connection->getClient();
        try {
            $response = $client->get($uri);
        } catch (GuzzleException $guzzleException) {
            throw new ApiException($guzzleException->getMessage());
        }

        return $this->parseResourceList($response);
    }

    /**
     * @return BaseResource[]
     */
    private function parseResourceList(ResponseInterface $response): array
    {
        $content = (string) $response->getBody();

        // XXX: some broken output...
        $content = str_replace("\x18", '', $content);

        try {
            $json = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new ApiException('json response invalid');
        }

        $resultList = [];
        foreach ($json['resource'] as $resource) {
            $content = $resource['content'];

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

            $resultList[] = new BaseResource($realContent, $this);
        }

        return $resultList;
    }

    public function getDateTimeZone(): \DateTimeZone
    {
        return $this->dateTimeZone;
    }
}
