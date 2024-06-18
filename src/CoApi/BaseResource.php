<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

class BaseResource
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Example: "LiveSync[Single|Normal]" or "LiveSync".
     */
    public function getSyncSource(): string
    {
        return $this->data['SOURCE'];
    }

    /**
     * Example: "13.06.2024T11:52:43".
     */
    public function getSyncTimestamp(): string
    {
        return $this->data['TIMESTAMP'];
    }
}
