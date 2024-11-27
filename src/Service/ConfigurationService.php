<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Service;

class ConfigurationService
{
    private array $config = [];

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getApiUrl(): string
    {
        return $this->config['api_url'];
    }

    public function getDataServiceStudents(): string
    {
        return $this->config['data_service_name_students'];
    }

    public function getDataServiceNameStudies(): string
    {
        return $this->config['data_service_name_studies'];
    }

    public function getDataServiceNameApplications(): string
    {
        return $this->config['data_service_name_applications'];
    }

    public function getClientId(): string
    {
        return $this->config['client_id'];
    }

    public function getClientSecret(): string
    {
        return $this->config['client_secret'];
    }

    /**
     * Exclude all inactive students and studies.
     */
    public function getExcludeInactive(): bool
    {
        return $this->config['internal']['exclude_inactive'];
    }

    public function getCacheEnabled(): bool
    {
        return $this->config['internal']['cache'];
    }

    /**
     * The timezone for the timestamps returned by the CO API.
     */
    public function getApiTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone($this->config['api_time_zone']);
    }

    /**
     * The page size used for CO requests.
     */
    public function getPageSize(): int
    {
        return $this->config['page_size'];
    }
}
