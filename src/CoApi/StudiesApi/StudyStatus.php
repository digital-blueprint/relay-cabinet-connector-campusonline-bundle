<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;

class StudyStatus
{
    private const TRANSLATIONS = [
        '#' => [
            'de' => 'Studium offen (und Meldung im Vorsemester vorhanden)',
        ],
        'a' => [
            'de' => 'noch nicht gemeldet - Studienphase begonnen',
        ],
        'B' => [
            'de' => 'gemeldet - Neueinschreibung',
        ],
        'E' => [
            'de' => 'gemeldet - Ersteinschreibung',
        ],
        'I' => [
            'de' => 'gemeldet',
        ],
        'o' => [
            'de' => 'noch nicht gemeldet - Studium offen',
        ],
        'R' => [
            'de' => 'Rücktritt von Meldung',
        ],
        'U' => [
            'de' => 'beurlaubt',
        ],
        'V' => [
            'de' => 'Verzicht auf Studienplatz',
        ],
        'X' => [
            'de' => 'geschlossen (Abschluss u./o. keine Fortsetzung möglich)',
        ],
        'y' => [
            'de' => 'noch nicht gemeldet - fortzusetzen (erschwert zu öffnen)',
        ],
        'Y' => [
            'de' => 'geschlossen (erschwert zu öffnen)',
        ],
        'z' => [
            'de' => 'noch nicht gemeldet - fortzusetzen',
        ],
        'Z' => [
            'de' => 'geschlossen (Antrag oder ex lege)',
        ],
        'f' => [
            'de' => 'fehler',
        ],
        'G' => [
            'de' => 'logisch gelöscht',
        ],
    ];

    public string $value;
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
