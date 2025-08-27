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
            'en' => 'Enrollment Open',
        ],
        'externe Person' => [
            'de' => 'Externe Person',
            'en' => 'External Person',
        ],
        'gültige/r Studierende/r' => [
            'de' => 'Gültige/r Studierende/r',
            'en' => 'Valid Student',
        ],
        'Studienberechtigungsprüfung' => [
            'de' => 'Studienberechtigungsprüfung',
            'en' => 'University Entrance Exam',
        ],
        'Testdaten' => [
            'de' => 'Testdaten',
            'en' => 'Test Data',
        ],
        'Voranmeldung' => [
            'de' => 'Voranmeldung',
            'en' => 'Pre-Registration',
        ],
        'Voranmeldung ohne Hochschulstatistik' => [
            'de' => 'Voranmeldung ohne Hochschulstatistik',
            'en' => 'Pre-Registration without University Statistics',
        ],
    ];

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getName(string $locale = 'en'): string
    {
        return Utils::getTranslatedText(self::TRANSLATIONS, $this->value, $locale, ['de' => $this->value]);
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
