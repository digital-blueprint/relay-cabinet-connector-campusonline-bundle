<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

/**
 * Represents a country.
 */
class Country
{
    public int $value;
    private ?array $fallbackTranslations;

    public function __construct(int $value, ?array $fallbackTranslations = null)
    {
        $this->value = $value;
        $this->fallbackTranslations = $fallbackTranslations;
    }

    public function getAlpha3Code(): ?string
    {
        return CountryUtils::getAlpha3Code($this->value);
    }

    public function getName(string $locale = 'en'): string
    {
        return CountryUtils::getName($this->value, $locale, $this->fallbackTranslations);
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
