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
        return $this->config['exclude_inactive'];
    }

    public function getCacheEnabled(): bool
    {
        return $this->config['cache'];
    }

    /**
     * The timezone for the timestamps returned by the CO API.
     */
    public function getApiTimeZone(): \DateTimeZone
    {
        // XXX: hardcode this for now, maybe make it configurable in the future
        return new \DateTimeZone('Europe/Vienna');
    }
}
