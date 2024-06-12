<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

use Dbp\CampusonlineApi\Rest\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\ApplicationsApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\StudentsApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\StudiesApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;

class SyncApi
{
    private ConfigurationService $config;

    private Connection $connection;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
        $this->connection = new Connection($this->config->getApiUrl(), $this->config->getClientId(), $this->config->getClientSecret());
    }

    public function setClientHandler(?object $handler, string $token): void
    {
        $this->connection->setClientHandler($handler);
        $this->connection->setToken($token);
    }

    public function getStudiesApi(): StudiesApi
    {
        return new StudiesApi($this->connection, $this->config->getDataServiceNameStudies());
    }

    public function getApplicationsApi(): ApplicationsApi
    {
        return new ApplicationsApi($this->connection, $this->config->getDataServiceNameApplications());
    }

    public function getStudentsApi(): StudentsApi
    {
        return new StudentsApi($this->connection, $this->config->getDataServiceStudents());
    }
}
