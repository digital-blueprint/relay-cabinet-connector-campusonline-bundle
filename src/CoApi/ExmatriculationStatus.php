<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

/**
 * Also called "Abmeldestatus".
 */
class ExmatriculationStatus
{
    public string $value;

    private const TRANSLATIONS = [
        // Personenbezogen / Personal
        'A' => [
            'de' => 'auf Antrag (A)',
        ],
        'E' => [
            'de' => 'ex lege (E)',
        ],
        'EZ' => [
            'de' => 'ex lege (EZ)',
        ],
        'G' => [
            'de' => 'sonst.Gründe (G)',
        ],
        'R' => [
            'de' => 'Rücktritt Imm. (R)',
        ],
        // Studienbezogen / Study-related
        'I' => [
            'de' => 'Stpl.Verzicht (I)',
        ],
        'MNTC' => [
            'de' => 'Schließung (MNTC)',
        ],
        'MSL' => [
            'de' => 'keine Mindeststudienleistung (MSL)',
        ],
        'U' => [
            'de' => 'Abbruch Studium (U)',
        ],
        'Y' => [
            'de' => 'n.bestand.Auflagen (Y)',
        ],
        'V' => [
            'de' => 'verstorben (V)',
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
