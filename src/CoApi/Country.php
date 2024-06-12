<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

/**
 * Represents a country.
 */
class Country
{
    public int $value;

    public function __construct(int $coId)
    {
        $this->value = $coId;
    }

    public static function fromId(int $id): Country
    {
        return new self($id);
    }

    public function getAlpha3Code(): ?string
    {
        return CountryUtils::getAlpha3Code($this->value);
    }

    public function getName(string $locale = 'en'): string
    {
        return CountryUtils::getName($this->value, $locale);
    }

    public function forJson(): array
    {
        return [
            'key' => (string) $this->value,
            'translations' => [
                'de' => $this->getName('de'),
                'en' => $this->getName('en'),
            ],
        ];
    }
}
