<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;

class PersonalStatus
{
    public string $value;

    private const TRANSLATIONS = [
        'Einschreibung offen' => [
            'de' => 'Einschreibung offen',
            'en' => 'enrollment open',
        ],
        'externe Person' => [
            'de' => 'externe Person',
            'en' => ' external person',
        ],
        'gültige/r Studierende/r' => [
            'de' => 'gültige/r Studierende/r',
            'en' => 'valid student',
        ],
        'Studienberechtigungsprüfung' => [
            'de' => 'Studienberechtigungsprüfung',
            'en' => 'university entrance exam',
        ],
        'Testdaten' => [
            'de' => 'Testdaten',
            'en' => 'test data',
        ],
        'Voranmeldung' => [
            'de' => 'Voranmeldung',
            'en' => 'pre-registration',
        ],
        'Voranmeldung ohne Hochschulstatistik' => [
            'de' => 'Voranmeldung ohne Hochschulstatistik',
            'en' => 'pre-registration without university statistics',
        ],
    ];

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getName(string $locale = 'en'): string
    {
        return Utils::getTranslatedText(self::TRANSLATIONS, $this->value, $locale);
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
