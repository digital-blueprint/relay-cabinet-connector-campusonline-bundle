<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Connection implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $clientHandler;

    private $token;

    public function __construct(string $baseUrl, string $clientId, string $clientSecret)
    {
        $this->baseUrl = $baseUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function setClientHandler(?object $handler): void
    {
        $this->clientHandler = $handler;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getClient(): Client
    {
        $token = $this->getToken();

        $stack = HandlerStack::create($this->clientHandler);
        $base_uri = $this->baseUrl;
        if (substr($base_uri, -1) !== '/') {
            $base_uri .= '/';
        }

        $client_options = [
            'base_uri' => $base_uri,
            'handler' => $stack,
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
        ];

        if ($this->logger !== null) {
            $stack->push(Utils::createLoggerMiddleware($this->logger));
        }

        $client = new Client($client_options);

        return $client;
    }

    private function getToken(): string
    {
        if ($this->token === null) {
            $this->refreshToken();
        }

        return $this->token;
    }

    private function refreshToken(): void
    {
        $stack = HandlerStack::create($this->clientHandler);
        $client_options = [
            'handler' => $stack,
        ];
        if ($this->logger !== null) {
            $stack->push(Utils::createLoggerMiddleware($this->logger));
        }
        $client = new Client($client_options);

        try {
            $response = $client->post($this->baseUrl.'/wbOAuth2.token', [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            throw new ApiException($guzzleException->getMessage());
        }
        $data = $response->getBody()->getContents();

        try {
            $token = json_decode($data, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new ApiException($exception->getMessage());
        }
        $this->token = $token['access_token'];
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
