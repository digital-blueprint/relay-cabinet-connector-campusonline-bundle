<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\Utils\CountryUtils;

/**
 * Represents a country, or some other special area (like "EU/EEA (without Austria)").
 */
class Nationality
{
    public int $value;

    public function __construct(int $coId)
    {
        $this->value = $coId;
    }

    public function getAlpha3Code(): ?string
    {
        return CountryUtils::getAlpha3Code($this->value);
    }

    public function getName(string $locale = 'en'): string
    {
        return CountryUtils::getName($this->value, $locale);
    }
}
