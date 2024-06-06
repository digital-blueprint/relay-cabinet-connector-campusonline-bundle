<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\PersonDataApi;

enum StudentStatus: string
{
    /**
     * Außerordentlich.
     */
    case Extraordinary = 'A';

    /**
     * Mitbelegend.
     */
    case CoRegistered = 'M';

    /**
     * nicht zugelassen.
     */
    case NotAdmitted = 'E';

    /**
     * Ordentlich.
     */
    case Regular = 'O';

    public function getName(string $locale = 'en'): string
    {
        $translations = [
            'de' => [
                self::Extraordinary->value => 'Außerordentlich',
                self::CoRegistered->value => 'Mitbelegend',
                self::NotAdmitted->value => 'nicht zugelassen',
                self::Regular->value => 'Ordentlich',
            ],
            'en' => [
                self::Extraordinary->value => 'extraordinary',
                self::CoRegistered->value => 'co-registered',
                self::NotAdmitted->value => 'not admitted',
                self::Regular->value => 'regular',
            ],
        ];

        return ($translations[$locale] ?? $translations['en'])[$this->value];
    }
}
