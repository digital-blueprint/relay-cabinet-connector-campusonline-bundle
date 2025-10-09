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
            'en' => 'acceptance of study place',
        ],
        'B' => [
            'de' => 'gemeldet - Neueinschreibung',
            'en' => 're-enrollment',
        ],
        'E' => [
            'de' => 'gemeldet - Ersteinschreibung',
            'en' => 'first enrollment',
        ],
        'I' => [
            'de' => 'gemeldet',
            'en' => 'registered',
        ],
        'o' => [
            'de' => 'noch nicht gemeldet - Studium offen',
        ],
        'R' => [
            'de' => 'Rücktritt von Meldung',
            'en' => 'withdrawal of enrollment',
        ],
        'U' => [
            'de' => 'beurlaubt',
            'en' => 'on leave',
        ],
        'V' => [
            'de' => 'Verzicht auf Studienplatz',
            'en' => 'temporarily admitted',
        ],
        'X' => [
            'de' => 'geschlossen (Abschluss u./o. keine Fortsetzung möglich)',
            'en' => 'closed (completed or continuation not possible)',
        ],
        'y' => [
            'de' => 'noch nicht gemeldet - fortzusetzen (erschwert zu öffnen)',
            'en' => 'to continue',
        ],
        'Y' => [
            'de' => 'geschlossen (erschwert zu öffnen)',
            'en' => 'closed (no re-admission)',
        ],
        'z' => [
            'de' => 'noch nicht gemeldet - fortzusetzen',
            'en' => 'to continue',
        ],
        'Z' => [
            'de' => 'geschlossen (Antrag oder ex lege)',
            'en' => 'closed (request or ex lege)',
        ],
        'f' => [
            'de' => 'fehler',
            'en' => 'error',
        ],
        'G' => [
            'de' => 'logisch gelöscht',
            'en' => 'logically deleted',
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
