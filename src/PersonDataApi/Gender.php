<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi;

enum Gender: string
{
    /**
     * Männlich.
     */
    case Male = 'M';

    /**
     * Weiblich.
     */
    case Female = 'W';

    /**
     * Divers (called "intersex" in the CO translation).
     */
    case NonBinary = 'X';

    /**
     * Unbekannt.
     */
    case Unknown = 'U';

    /**
     * Offen.
     */
    case Open = 'O';

    /**
     * Inter.
     */
    case Inter = 'I';

    /**
     * Kein Eintrag.
     */
    case NoRecord = 'K';

    public function getName(string $locale = 'en'): string
    {
        $translations = [
            'de' => [
                self::Male->value => 'Männlich',
                self::Female->value => 'Weiblich',
                self::NonBinary->value => 'Divers',
                self::Unknown->value => 'Unbekannt',
                self::Open->value => 'Offen',
                self::Inter->value => 'Inter',
                self::NoRecord->value => 'Kein Eintrag',
            ],
            'en' => [
                self::Male->value => 'male',
                self::Female->value => 'female',
                self::NonBinary->value => 'non-binary',
                self::Unknown->value => 'unknown',
                self::Open->value => 'open',
                self::Inter->value => 'inter',
                self::NoRecord->value => 'no record',
            ],
        ];

        return ($translations[$locale] ?? $translations['en'])[$this->value];
    }
}
