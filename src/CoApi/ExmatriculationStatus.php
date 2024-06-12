<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

/**
 * Also called "Abmeldestatus".
 */
class ExmatriculationStatus
{
    public string $value;

    public function __construct(string $coId)
    {
        $this->value = $coId;
    }

    public static function fromId(string $id): ExmatriculationStatus
    {
        return new self($id);
    }

    public function getName(string $locale = 'en'): string
    {
        $translations = [
            'de' => [
                // Personenbezogen
                'A' => 'auf Antrag (A)',
                'E' => 'ex lege (E)',
                'EZ' => 'ex lege (EZ)',
                'G' => 'sonst.Gründe (G)',
                'R' => 'Rücktritt Imm. (R)',
                // Studienbezogen
                'I' => 'Stpl.Verzicht (I)',
                'MNTC' => 'Schließung (MNTC)',
                'MSL' => 'keine Mindeststudienleistung (MSL)',
                'U' => 'Abbruch Studium (U)',
                'Y' => 'n.bestand.Auflagen (Y)',
                'V' => 'verstorben (V)',
            ],
            'en' => [
                // Personal
                'A' => 'auf Antrag (A)',
                'E' => 'ex lege (E)',
                'EZ' => 'ex lege (EZ)',
                'G' => 'sonst.Gründe (G)',
                'R' => 'Rücktritt Imm. (R)',
                // Study-related
                'I' => 'Stpl.Verzicht (I)',
                'MNTC' => 'Schließung (MNTC)',
                'MSL' => 'keine Mindeststudienleistung (MSL)',
                'U' => 'Abbruch Studium (U)',
                'Y' => 'n.bestand.Auflagen (Y)',
                'V' => 'verstorben (V)',
            ],
        ];

        return ($translations[$locale] ?? $translations['en'])[$this->value];
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
