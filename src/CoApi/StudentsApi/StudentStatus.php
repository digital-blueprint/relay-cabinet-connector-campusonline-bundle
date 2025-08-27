<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;

class StudentStatus
{
    public string $value;

    private const TRANSLATIONS = [
        'A' => [
            'de' => 'Außerordentlich',
            'en' => 'Extraordinary',
        ],
        'M' => [
            'de' => 'Mitbelegend',
            'en' => 'Co-Registered',
        ],
        'E' => [
            'de' => 'Nicht Zugelassen',
            'en' => 'Not Admitted',
        ],
        'O' => [
            'de' => 'Ordentlich',
            'en' => 'Regular',
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
