<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;

class Gender
{
    public string $value;

    private const TRANSLATIONS = [
        'M' => [
            'de' => 'Männlich',
            'en' => 'male',
        ],
        'W' => [
            'de' => 'Weiblich',
            'en' => 'female',
        ],
        'X' => [
            'de' => 'Divers', // (called "intersex" in the CO translation).
            'en' => 'non-binary',
        ],
        'U' => [
            'de' => 'Unbekannt',
            'en' => 'unknown',
        ],
        'O' => [
            'de' => 'Offen',
            'en' => 'open',
        ],
        'I' => [
            'de' => 'Inter',
            'en' => 'inter',
        ],
        'K' => [
            'de' => 'Kein Eintrag',
            'en' => 'no record',
        ],
    ];

    private ?array $fallbackTranslations;

    public function __construct(string $value, ?array $fallbackTranslations = null)
    {
        $this->value = $value;
        $this->fallbackTranslations = $fallbackTranslations;
    }

    public function getName(string $locale = 'en'): string
    {
        return Utils::getTranslatedText(self::TRANSLATIONS, $this->value, $locale, $this->fallbackTranslations);
    }

    public function forJson(): array
    {
        return [
            'key' => $this->value,
            'translations' => [
                'de' => $this->getName('de'),
                'en' => $this->getName('en'),
            ],
        ];
    }
}
