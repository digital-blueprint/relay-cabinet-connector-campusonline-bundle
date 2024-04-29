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

    public function getDataServiceNamePersonData(): string
    {
        return $this->config['data_service_name_person_data'];
    }

    public function getDataServiceNameActiveStudies(): string
    {
        return $this->config['data_service_name_active_studies'];
    }

    public function getClientId(): string
    {
        return $this->config['client_id'];
    }

    public function getClientSecret(): string
    {
        return $this->config['client_secret'];
    }
}
