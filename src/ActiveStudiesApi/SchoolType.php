<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\ActiveStudiesApi;

class SchoolType
{
    public string $value;

    private const UNKOWN = [
        'de' => 'Unbekannt',
        'en' => 'Unknown',
    ];

    private const TRANSLATIONS = [
        '01' => [
            'de' => 'Gymnasium',
            'en' => 'Gymnasium',
        ],
        '02' => [
            'de' => 'Humanistisches Gymnasium',
            'en' => 'Humanistisches Gymnasium',
        ],
        '03' => [
            'de' => 'Neusprachliches Gymnasium',
            'en' => 'Neusprachliches Gymnasium',
        ],
        '04' => [
            'de' => 'Realistisches Gymnasium',
            'en' => 'Realistisches Gymnasium',
        ],
        '05' => [
            'de' => 'Realgymnasium',
            'en' => 'Realgymnasium',
        ],
        '06' => [
            'de' => 'Naturwissensch. Realgymnasium',
            'en' => 'Naturwissensch. Realgymnasium',
        ],
        '07' => [
            'de' => 'Mathematisches Realgymnasium',
            'en' => 'Mathematisches Realgymnasium',
        ],
        '08' => [
            'de' => 'Oberstufenrealgymnasium',
            'en' => 'Oberstufenrealgymnasium',
        ],
        '09' => [
            'de' => 'Wirtschaftskundl.Realgymnasium',
            'en' => 'Wirtschaftskundl.Realgymnasium',
        ],
        '10' => [
            'de' => 'Realschule',
            'en' => 'Realschule',
        ],
        '11' => [
            'de' => 'Frauenoberschule',
            'en' => 'Frauenoberschule',
        ],
        '12' => [
            'de' => 'Aufbaugymnasium',
            'en' => 'Aufbaugymnasium',
        ],
        '13' => [
            'de' => 'Aufbaurealgymnasium',
            'en' => 'Aufbaurealgymnasium',
        ],
        '14' => [
            'de' => 'Aufbaumittelschule',
            'en' => 'Aufbaumittelschule',
        ],
        '15' => [
            'de' => 'Gymnasium für Berufstätige',
            'en' => 'Gymnasium für Berufstätige',
        ],
        '16' => [
            'de' => '(Wirtsch.kundl.) RG f.Berufst.',
            'en' => '(Wirtsch.kundl.) RG f.Berufst.',
        ],
        '17' => [
            'de' => 'Arbeitermittelschule',
            'en' => 'Arbeitermittelschule',
        ],
        '18' => [
            'de' => 'Berufsreifeprüfung',
            'en' => 'higher education entrance qualification',
        ],
        '19' => [
            'de' => 'H.techn.u.gewerbl. Lehranstalt',
            'en' => 'H.techn.u.gewerbl. Lehranstalt',
        ],
        '20' => [
            'de' => 'Handelsakademie',
            'en' => 'Handelsakademie',
        ],
        '21' => [
            'de' => 'H.Lehranst. f.wirtsch. Berufe',
            'en' => 'H.Lehranst. f.wirtsch. Berufe',
        ],
        '22' => [
            'de' => 'Lehrerbildungsanstalt',
            'en' => 'Lehrerbildungsanstalt',
        ],
        '23' => [
            'de' => 'H.land- u.forstwirt. Lehranst.',
            'en' => 'H.land- u.forstwirt. Lehranst.',
        ],
        '24' => [
            'de' => 'Studienberechtigungsprüfung',
            'en' => 'university entrance qualification exam',
        ],
        '25' => [
            'de' => 'ausländische Reifeprüfung',
            'en' => 'foreign secondary school leaving exam',
        ],
        '26' => [
            'de' => 'BA für Sozialpädagogik',
            'en' => 'BA für Sozialpädagogik',
        ],
        '27' => [
            'de' => 'Externistenreifeprüfung',
            'en' => 'secondary school leaving examination (external pupil)',
        ],
        '28' => [
            'de' => 'BA f.Elementarpädagogik',
            'en' => 'BA f.Elementarpädagogik',
        ],
        '29' => [
            'de' => 'Akademie',
            'en' => 'college',
        ],
        '30' => [
            'de' => 'inl. postsekund. Bildungseinr.',
            'en' => 'Austr. post-second. educ. inst.',
        ],
        '31' => [
            'de' => 'ausl. postsekund.Bildungseinr.',
            'en' => 'foreign post-second. educ. inst.',
        ],
        '32' => [
            'de' => 'inl. FH-Diplom-/MA-Studiengang',
            'en' => 'Diploma / Master programme at Austrian university of applied sciences',
        ],
        '33' => [
            'de' => 'inl. akkred. Privatuniversität',
            'en' => 'Austr. accred. private university',
        ],
        '34' => [
            'de' => 'inl. Pädagogische Hochschule',
            'en' => 'Austr. university of education',
        ],
        '35' => [
            'de' => 'Reife/Koop.-Vertrag (Ausland)',
            'en' => 'university entrance qualification according to the co-operation agreement (foreign)',
        ],
        '36' => [
            'de' => 'gilt als inländisch',
            'en' => 'status equal to Austrian',
        ],
        '37' => [
            'de' => 'inl. FH-Bachelorstudiengang',
            'en' => 'Bachelor programme at Austrian university of applied sciences',
        ],
        '38' => [
            'de' => 'Bachelorstud. and. inl. Univ.',
            'en' => 'Bachelor study programme at other Austrian university',
        ],
        '39' => [
            'de' => 'Bachelorstudium eigene Univ.',
            'en' => 'Bachelor study programme at own university',
        ],
        '40' => [
            'de' => 'Master-/Diplomst.and.inl.Univ.',
            'en' => 'Master/ Diploma study programme at other Austrian university',
        ],
        '41' => [
            'de' => 'Master-/Diplomst.eigene Univ.',
            'en' => 'Master/ Diploma study programme at own university',
        ],
        '42' => [
            'de' => 'künstler. Zulassungsprüfung',
            'en' => 'artistic admission examination',
        ],
        '43' => [
            'de' => 'ausl. beschränkte HS-Reife',
            'en' => 'foreign restricted university qualification',
        ],
        '44' => [
            'de' => 'Studienberecht. HG/HStudBerG',
            'en' => 'Univ. entrance qualification HStudBerG',
        ],
        '46' => [
            'de' => 'IB Diploma (Ausland)',
            'en' => 'IB Diploma (foreign country)',
        ],
        '47' => [
            'de' => 'IB Diploma (Inland)',
            'en' => 'IB Diploma (home country)',
        ],
        '48' => [
            'de' => 'Europäisches Abitur (Ausland)',
            'en' => 'European school leaving examination (foreign country)',
        ],
        '49' => [
            'de' => 'Europäisches Abitur (Inland)',
            'en' => 'European school leaving examination (home country)',
        ],
        '50' => [
            'de' => 'BA-Stud.bes.Stud.erf.eig.Uni. ',
            'en' => 'Bachelor\'s degree programme / exceptional academic success at own university',
        ],
        '51' => [
            'de' => 'BA-Stud.bes.Stud.erf.and.BE',
            'en' => 'Bachelor\'s degree programme / exceptional academic success at other Austrian university ',
        ],
        '52' => [
            'de' => 'Reife/Koop.-Vertrag (Inland)',
            'en' => 'university entrance qualification according to the co-operation agreement (domestic)',
        ],
        '53' => [
            'de' => 'Ao. BA gem. § 56 (2) UG 2002',
            'en' => 'Ao. BA gem. § 56 (2) UG 2002',
        ],
        '54' => [
            'de' => 'Ao. MA gem. § 56 (2) UG 2002',
            'en' => 'Ao. MA gem. § 56 (2) UG 2002',
        ],
        '55' => [
            'de' => 'Berufl. Qual./Berufserfahrung',
            'en' => 'Berufl. Qual./Berufserfahrung',
        ],
        '56' => [
            'de' => 'Doktoratsstud. and. inl. Univ.',
            'en' => 'Doktoratsstud. and. inl. Univ.',
        ],
        '57' => [
            'de' => 'Doktoratsstud. eigene Univ.',
            'en' => 'Doktoratsstud. eigene Univ.',
        ],
        '98' => [
            'de' => 'Reifeprüfung nicht relevant',
            'en' => 'school leaving exam not relevant',
        ],
        '99' => [
            'de' => 'keine Reifeprüfung',
            'en' => 'no school leaving certificate',
        ],
    ];

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromId(string $id): SchoolType
    {
        return new self($id);
    }

    public function getName(string $locale = 'en'): string
    {
        // Falls back to "en" and then just the last value
        $getTranslated = function (array $mapping, string $locale) {
            return $mapping[$locale] ?? ($mapping['en'] ?? array_values($mapping)[0]);
        };

        // Get the translated value
        $specialMapping = self::TRANSLATIONS[$this->value] ?? null;
        if ($specialMapping !== null) {
            return $getTranslated($specialMapping, $locale);
        }

        // Unknown, fall back to manual and include the ID in the name
        return $getTranslated(self::UNKOWN, $locale).' ('.$this->value.')';
    }
}
