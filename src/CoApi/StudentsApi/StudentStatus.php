<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;

class StudentStatus
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getName(string $locale = 'en'): string
    {
        $translations = [
            'A' => [
                'de' => 'Außerordentlich',
                'en' => 'extraordinary',
            ],
            'M' => [
                'de' => 'Mitbelegend',
                'en' => 'co-registered',
            ],
            'E' => [
                'de' => 'nicht zugelassen',
                'en' => 'not admitted',
            ],
            'O' => [
                'de' => 'Ordentlich',
                'en' => 'regular',
            ],
        ];

        return Utils::getTranslatedText($translations, $this->value, $locale);
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
