<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

use Dbp\CampusonlineApi\Rest\Connection;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\ApplicationsApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\StudentsApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\StudiesApi;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\Service\ConfigurationService;
use League\Uri\UriTemplate;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class CoApi implements LoggerAwareInterface
{
    private ConfigurationService $config;

    private Connection $connection;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
        $this->connection = new Connection($this->config->getApiUrl(), $this->config->getClientId(), $this->config->getClientSecret());
    }

    public function setClientHandler(?object $handler, ?string $token): void
    {
        $this->connection->setClientHandler($handler);
        if ($token !== null) {
            $this->connection->setToken($token);
        }
    }

    public function getStudiesApi(): StudiesApi
    {
        return new StudiesApi($this->connection, $this->config->getDataServiceNameStudies(), $this->config->getApiTimeZone());
    }

    public function getApplicationsApi(): ApplicationsApi
    {
        return new ApplicationsApi($this->connection, $this->config->getDataServiceNameApplications(), $this->config->getApiTimeZone());
    }

    public function getStudentsApi(): StudentsApi
    {
        return new StudentsApi($this->connection, $this->config->getDataServiceStudents(), $this->config->getApiTimeZone());
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->connection->setLogger($logger);
    }

    /**
     * Returns a URL to a website for displaying and editing the source student data.
     */
    public function getStudentWebUrl(Student $student): string
    {
        $apiUrl = $this->config->getApiUrl();
        $uriTemplate = new UriTemplate(rtrim($apiUrl, '/').'/wbStEvidenz.StEvi{?pStPersonNr}');

        return (string) $uriTemplate->expand([
            'pStPersonNr' => $student->getStudentPersonNumber(),
        ]);
    }

    /**
     * Returns a URL to a website for displaying and editing the source study data.
     */
    public function getStudyWebUrl(Study $study): string
    {
        $apiUrl = $this->config->getApiUrl();
        $uriTemplate = new UriTemplate(rtrim($apiUrl, '/').'/wbStmStudiendaten.wbStudiendetails{?pStPersonNr,pStStudiumNr}');

        return (string) $uriTemplate->expand([
            'pStPersonNr' => $study->getStudentPersonNumber(),
            'pStStudiumNr' => $study->getStudyNumber(),
        ]);
    }
}
