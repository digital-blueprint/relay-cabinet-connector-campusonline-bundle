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
            'en' => 'Male',
        ],
        'W' => [
            'de' => 'Weiblich',
            'en' => 'Female',
        ],
        'X' => [
            'de' => 'Divers', // (called "intersex" in the CO translation).
            'en' => 'Non-Binary',
        ],
        'U' => [
            'de' => 'Unbekannt',
            'en' => 'Unknown',
        ],
        'O' => [
            'de' => 'Offen',
            'en' => 'Open',
        ],
        'I' => [
            'de' => 'Inter',
            'en' => 'Inter',
        ],
        'K' => [
            'de' => 'Kein Eintrag',
            'en' => 'No Record',
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
