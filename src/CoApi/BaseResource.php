<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

class BaseResource
{
    public array $data;
    protected BaseApi $baseApi;

    public function __construct(array $data, BaseApi $baseApi)
    {
        $this->data = $data;
        $this->baseApi = $baseApi;
    }

    /**
     * Example: "LiveSync[Single|Normal]" or "LiveSync".
     */
    public function getSyncSource(): string
    {
        return $this->data['SOURCE'];
    }

    /**
     * Whether the resource is live, or fetched from some possibly out of date snapshot.
     */
    public function isLiveData(): bool
    {
        return \str_starts_with($this->getSyncSource(), 'LiveSync');
    }

    /**
     * Example: "13.06.2024T11:52:43".
     */
    public function getSyncTimestampString(): string
    {
        return $this->data['TIMESTAMP'];
    }

    public function getSyncTimestamp(): \DateTimeInterface
    {
        return Utils::syncTimestampToDateTimeUTC($this->data['TIMESTAMP'], $this->baseApi->getDateTimeZone());
    }
}
