<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Utils;

class AdditionalExam
{
    public string $value;

    private const TRANSLATIONS = [
        'ABA' => [
            'de' => 'Befristet - Zugelassen',
            'en' => 'limited - admitted',
        ],
        'AZP' => [
            'de' => 'Zul.Prfg. - Zulassungsprüfung',
            'en' => 'adm.exam. - admission examination',
        ],
        'EBE' => [
            'de' => 'Erg.Prfg. - Bildnerische Erziehung',
            'en' => 'suppl.exam. - Arts',
        ],
        'EBU' => [
            'de' => 'Erg.Prfg. - Biologie und Umweltkunde',
            'en' => 'suppl.exam. - Biology and Ecology',
        ],
        'ECH' => [
            'de' => 'Erg.Prfg. - Chemie',
            'en' => 'suppl.exam. - Chemistry',
        ],
        'ED' => [
            'de' => 'Erg.Prfg. - Deutsch',
            'en' => 'suppl.exam. - German',
        ],
        'EDG' => [
            'de' => 'Erg.Prfg. - Darstellende Geometrie',
            'en' => 'suppl.exam. - Descriptive Geometry',
        ],
        'EE' => [
            'de' => 'Erg.Prfg. - Englisch',
            'en' => 'suppl.exam. - English',
        ],
        'EF' => [
            'de' => 'Erg.Prfg. - Französisch',
            'en' => 'suppl.exam. - French',
        ],
        'EFS' => [
            'de' => 'Erg.Prfg. - Erste Fremdsprache',
            'en' => 'suppl.exam. - First Foreign Language',
        ],
        'EGR' => [
            'de' => 'Erg.Prfg. - Griechisch',
            'en' => 'suppl.exam. - Greek',
        ],
        'EGS' => [
            'de' => 'Erg.Prfg. - Geschichte und Sozialkunde',
            'en' => 'suppl.exam. - History and Social Studies',
        ],
        'EGW' => [
            'de' => 'Erg.Prfg. - Geographie u. Wirtschaftskunde',
            'en' => 'suppl.exam. - Geography and Economics',
        ],
        'EL' => [
            'de' => 'Erg.Prfg. - Latein',
            'en' => 'suppl.exam. - Latin',
        ],
        'EM' => [
            'de' => 'Erg.Prfg. - Mathematik',
            'en' => 'suppl.exam. - Mathematics',
        ],
        'EMU' => [
            'de' => 'Erg.Prfg. - Mutter-(Bildungs-)Sprache',
            'en' => 'suppl.exam. - mother tongue / education language',
        ],
        'EPE' => [
            'de' => 'Erg.Prfg. - Psychologie und Philosophie',
            'en' => 'suppl.exam. - Psychology and Philosophy',
        ],
        'EPH' => [
            'de' => 'Erg.Prfg. - Physik',
            'en' => 'suppl.exam. - Physics',
        ],
        'ERL' => [
            'de' => 'Erg.Prfg. - Religion',
            'en' => 'suppl.exam. - Religion',
        ],
        'NBB' => [
            'de' => 'Nachweis - Buchhaltung und Bilanzierung',
            'en' => 'proof - Accounting and Balancing',
        ],
        'ND' => [
            'de' => 'Nachweis - Deutschkenntnisse',
            'en' => 'proof - knowledge of German',
        ],
        'NKE' => [
            'de' => 'Nachweis - Körperlich-Motorische Eignung',
            'en' => 'proof - physical aptitude',
        ],
        'NKR' => [
            'de' => 'Nachweis - Kostenrechnung',
            'en' => 'proof - Cost Accounting',
        ],
        'NLF' => [
            'de' => 'Nachweis - Einer lebenden Fremdsprache',
            'en' => 'proof - of one modern foreign language',
        ],
        'NMB' => [
            'de' => 'Nachweis - Musikalische Begabung',
            'en' => 'proof - musical talent',
        ],
        'NMS' => [
            'de' => 'Nachweis - Maschinschr.D.Mutter-(B.-)Spr.',
            'en' => 'proof - Typewriting in mother tongue / education language',
        ],
        'NRW' => [
            'de' => 'Nachweis - Rechnungswesen',
            'en' => 'proof - Accounting',
        ],
        'NSF' => [
            'de' => 'Nachweis - Steno. der 1. Fremdsprache',
            'en' => 'proof - Shorthand in the first foreign language',
        ],
        'NSM' => [
            'de' => 'Nachweis - Steno. der Mutter-(Bild.-)Spr.',
            'en' => 'proof - Shorthand in the mother tongue / education language',
        ],
        'NVB' => [
            'de' => 'Nachweis - Visuelle Begabung',
            'en' => 'proof - visual talent',
        ],
        'ZBE' => [
            'de' => 'Zus.Prfg. - Bildnerische Erziehung',
            'en' => 'add.exam. - Arts',
        ],
        'ZBU' => [
            'de' => 'Zus.Prfg. - Biologie und Umweltkunde',
            'en' => 'add.exam. - Biology and Ecology',
        ],
        'ZDG' => [
            'de' => 'Zus.Prfg. - Darstellende Geometrie',
            'en' => 'add.exam. - Descriptive Geometry',
        ],
        'ZE' => [
            'de' => 'Zus.Prfg. - Englisch',
            'en' => 'add.exam. - English',
        ],
        'ZF' => [
            'de' => 'Zus.Prfg. - Französisch',
            'en' => 'add.exam. - French',
        ],
        'ZGR' => [
            'de' => 'Zus.Prfg. - Griechisch',
            'en' => 'add.exam. - Greek',
        ],
        'ZL' => [
            'de' => 'Zus.Prfg. - Latein',
            'en' => 'add.exam. - Latin',
        ],
        'ZM' => [
            'de' => 'Zus.Prfg. - Mathematik',
            'en' => 'add.exam. - Mathematics',
        ],
        'ZPE' => [
            'de' => 'Zus.Prfg. - Psychologie und Philosophie',
            'en' => 'add.exam. - Psychology and Philosophy',
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
