<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi;

use Symfony\Component\Intl\Countries;

class CountryUtils
{
    /**
     * Maps CO IDs to ISO 3166-1 alpha-3 codes.
     */
    private const COUNTRY_MAPPING_ALPHA3 = [
        /* Burma */
        1 => 'BUR',
        /* Tschechoslowakei */
        3 => 'CSK',
        /* Deutsche Demokratische Republik (DDR) */
        4 => 'DDR',
        /* Nordmazedonien */
        6 => 'MKD',
        /* Neutrale Zone */
        7 => 'NTZ',
        /* Sowjetunion (UdSSR) */
        8 => 'SUN',
        /* Jemen, Demokratische Republik (Südjemen) */
        9 => 'YMD',
        /* Afghanistan */
        10 => 'AFG',
        /* Ägypten */
        11 => 'EGY',
        /* Albanien */
        12 => 'ALB',
        /* Algerien */
        13 => 'DZA',
        /* Amerikanische Jungferninseln */
        14 => 'VIR',
        /* Amerikanische Überseeinseln, Kleinere */
        15 => 'UMI',
        /* Amerikanisch-Samoa */
        16 => 'ASM',
        /* Andorra */
        17 => 'AND',
        /* Angola */
        18 => 'AGO',
        /* Anguilla */
        19 => 'AIA',
        /* Antarktis */
        20 => 'ATA',
        /* Antigua und Barbuda */
        21 => 'ATG',
        /* Äquatorialguinea */
        22 => 'GNQ',
        /* Argentinien */
        23 => 'ARG',
        /* Armenien */
        24 => 'ARM',
        /* Aruba */
        25 => 'ABW',
        /* Aserbaidschan */
        26 => 'AZE',
        /* Äthiopien */
        27 => 'ETH',
        /* Australien */
        28 => 'AUS',
        /* Bahamas */
        29 => 'BHS',
        /* Bahrain */
        30 => 'BHR',
        /* Bangladesch */
        31 => 'BGD',
        /* Barbados */
        32 => 'BRB',
        /* Belarus (Weißrussland) */
        33 => 'BLR',
        /* Belgien */
        34 => 'BEL',
        /* Belize */
        35 => 'BLZ',
        /* Benin */
        36 => 'BEN',
        /* Bermuda */
        37 => 'BMU',
        /* Bhutan */
        38 => 'BTN',
        /* Bolivien */
        39 => 'BOL',
        /* Bosnien und Herzegowina */
        40 => 'BIH',
        /* Botsuana */
        41 => 'BWA',
        /* Bouvetinsel */
        42 => 'BVT',
        /* Brasilien */
        43 => 'BRA',
        /* Britische Jungferninseln */
        44 => 'VGB',
        /* Britisches Territorium im Indischen Ozean */
        45 => 'IOT',
        /* Brunei Darussalam */
        46 => 'BRN',
        /* Bulgarien */
        47 => 'BGR',
        /* Burkina Faso */
        48 => 'BFA',
        /* Burundi */
        49 => 'BDI',
        /* Chile */
        50 => 'CHL',
        /* China */
        51 => 'CHN',
        /* Cookinseln */
        52 => 'COK',
        /* Costa Rica */
        53 => 'CRI',
        /* Cote d'Ivoire (Elfenbeinküste) */
        54 => 'CIV',
        /* Dänemark */
        55 => 'DNK',
        /* Deutschland */
        56 => 'DEU',
        /* Dominica */
        57 => 'DMA',
        /* Dominikanische Republik */
        58 => 'DOM',
        /* Dschibuti */
        59 => 'DJI',
        /* Ecuador */
        60 => 'ECU',
        /* El Salvador */
        61 => 'SLV',
        /* Eritrea */
        62 => 'ERI',
        /* Estland */
        63 => 'EST',
        /* Falklandinseln (Malwinen) */
        64 => 'FLK',
        /* Färöer Inseln */
        65 => 'FRO',
        /* Fidschi */
        66 => 'FJI',
        /* Finnland */
        67 => 'FIN',
        /* Frankreich */
        68 => 'FRA',
        /* Französische Südseegebiete */
        70 => 'ATF',
        /* Französisch-Guayana */
        71 => 'GUF',
        /* Französisch-Polynesien */
        72 => 'PYF',
        /* Gabun */
        73 => 'GAB',
        /* Gambia */
        74 => 'GMB',
        /* Georgien */
        75 => 'GEO',
        /* Ghana */
        76 => 'GHA',
        /* Gibraltar */
        77 => 'GIB',
        /* Grenada */
        78 => 'GRD',
        /* Griechenland */
        79 => 'GRC',
        /* Grönland */
        80 => 'GRL',
        /* Guadeloupe */
        81 => 'GLP',
        /* Guam */
        82 => 'GUM',
        /* Guatemala */
        83 => 'GTM',
        /* Guinea */
        84 => 'GIN',
        /* Guinea-Bissau */
        85 => 'GNB',
        /* Guyana */
        86 => 'GUY',
        /* Haiti */
        87 => 'HTI',
        /* Heard und die McDonaldinseln */
        88 => 'HMD',
        /* Honduras */
        89 => 'HND',
        /* Hongkong */
        90 => 'HKG',
        /* Indien */
        91 => 'IND',
        /* Indonesien */
        92 => 'IDN',
        /* Irak */
        93 => 'IRQ',
        /* Iran */
        94 => 'IRN',
        /* Irland */
        95 => 'IRL',
        /* Island */
        96 => 'ISL',
        /* Israel */
        97 => 'ISR',
        /* Italien */
        98 => 'ITA',
        /* Jamaika */
        99 => 'JAM',
        /* Japan */
        100 => 'JPN',
        /* Jemen */
        101 => 'YEM',
        /* Jordanien */
        102 => 'JOR',
        /* Jugoslawien */
        103 => 'YUG',
        /* Cayman Inseln */
        104 => 'CYM',
        /* Kambodscha */
        105 => 'KHM',
        /* Kamerun */
        106 => 'CMR',
        /* Kanada */
        107 => 'CAN',
        /* Cabo Verde */
        108 => 'CPV',
        /* Kasachstan */
        109 => 'KAZ',
        /* Katar */
        110 => 'QAT',
        /* Kenia */
        111 => 'KEN',
        /* Kirgisistan */
        112 => 'KGZ',
        /* Kiribati */
        113 => 'KIR',
        /* Kokosinseln (Keeling) */
        114 => 'CCK',
        /* Kolumbien */
        115 => 'COL',
        /* Komoren */
        116 => 'COM',
        /* Kongo, Demokratische Republik */
        117 => 'COD',
        /* Korea, Demokratische Volksrepublik */
        118 => 'PRK',
        /* Korea, Republik */
        119 => 'KOR',
        /* Kroatien */
        120 => 'HRV',
        /* Kuba */
        121 => 'CUB',
        /* Kuwait */
        122 => 'KWT',
        /* Laos */
        123 => 'LAO',
        /* Lesotho */
        124 => 'LSO',
        /* Lettland */
        125 => 'LVA',
        /* Libanon */
        126 => 'LBN',
        /* Liberia */
        127 => 'LBR',
        /* Libyen */
        128 => 'LBY',
        /* Liechtenstein */
        129 => 'LIE',
        /* Litauen */
        130 => 'LTU',
        /* Luxemburg */
        131 => 'LUX',
        /* Macau */
        132 => 'MAC',
        /* Madagaskar */
        133 => 'MDG',
        /* Malawi */
        134 => 'MWI',
        /* Malaysia */
        135 => 'MYS',
        /* Malediven */
        136 => 'MDV',
        /* Mali */
        137 => 'MLI',
        /* Malta */
        138 => 'MLT',
        /* Marianen, Nördliche */
        139 => 'MNP',
        /* Marokko */
        140 => 'MAR',
        /* Marshallinseln */
        141 => 'MHL',
        /* Martinique */
        142 => 'MTQ',
        /* Mauretanien */
        143 => 'MRT',
        /* Mauritius */
        144 => 'MUS',
        /* Mayotte */
        145 => 'MYT',
        /* Mexiko */
        146 => 'MEX',
        /* Mikronesien (Föderierte Staaten von) */
        147 => 'FSM',
        /* Moldau */
        148 => 'MDA',
        /* Monaco */
        149 => 'MCO',
        /* Mongolei */
        150 => 'MNG',
        /* Montserrat */
        151 => 'MSR',
        /* Mosambik */
        152 => 'MOZ',
        /* Myanmar */
        153 => 'MMR',
        /* Namibia */
        154 => 'NAM',
        /* Nauru */
        155 => 'NRU',
        /* Nepal */
        156 => 'NPL',
        /* Neukaledonien */
        157 => 'NCL',
        /* Neuseeland */
        158 => 'NZL',
        /* Nicaragua */
        159 => 'NIC',
        /* Niederlande */
        160 => 'NLD',
        /* Curaçao */
        161 => 'CUW',
        /* Niger */
        162 => 'NER',
        /* Nigeria */
        163 => 'NGA',
        /* Niue */
        164 => 'NIU',
        /* Norfolkinseln */
        165 => 'NFK',
        /* Norwegen */
        166 => 'NOR',
        /* Oman */
        167 => 'OMN',
        /* Österreich */
        168 => 'AUT',
        /* Timor-Leste */
        169 => 'TLS',
        /* Pakistan */
        170 => 'PAK',
        /* Palau */
        171 => 'PLW',
        /* Panama */
        172 => 'PAN',
        /* Papua-Neuguinea */
        173 => 'PNG',
        /* Paraguay */
        174 => 'PRY',
        /* Peru */
        175 => 'PER',
        /* Philippinen */
        176 => 'PHL',
        /* Pitcairninseln */
        177 => 'PCN',
        /* Polen */
        178 => 'POL',
        /* Portugal */
        179 => 'PRT',
        /* Puerto Rico */
        180 => 'PRI',
        /* Réunion */
        181 => 'REU',
        /* Ruanda */
        182 => 'RWA',
        /* Rumänien */
        183 => 'ROU',
        /* Russische Föderation */
        184 => 'RUS',
        /* Salomonen */
        185 => 'SLB',
        /* Sambia */
        186 => 'ZMB',
        /* Samoa */
        187 => 'WSM',
        /* San Marino */
        188 => 'SMR',
        /* Sao Tomé und Principe */
        189 => 'STP',
        /* Saudi-Arabien */
        190 => 'SAU',
        /* Schweden */
        191 => 'SWE',
        /* Schweiz */
        192 => 'CHE',
        /* Senegal */
        193 => 'SEN',
        /* Seychellen */
        194 => 'SYC',
        /* Sierra Leone */
        195 => 'SLE',
        /* Simbabwe */
        196 => 'ZWE',
        /* Singapur */
        197 => 'SGP',
        /* Slowakei */
        198 => 'SVK',
        /* Slovenien */
        199 => 'SVN',
        /* Somalia */
        200 => 'SOM',
        /* Spanien */
        201 => 'ESP',
        /* Sri Lanka */
        202 => 'LKA',
        /* St. Helena */
        203 => 'SHN',
        /* St. Kitts und Nevis */
        204 => 'KNA',
        /* St. Lucia */
        205 => 'LCA',
        /*  St. Pierre und Miquelon */
        206 => 'SPM',
        /* St. Vincent und die Grenadinen */
        207 => 'VCT',
        /* Südafrika */
        208 => 'ZAF',
        /* Sudan */
        209 => 'SDN',
        /* Südgeorgien und die südlichen Sandwichinseln */
        210 => 'SGS',
        /* Suriname */
        211 => 'SUR',
        /* Svalbard und Jan Mayen */
        212 => 'SJM',
        /* Eswatini */
        213 => 'SWZ',
        /* Syrien */
        214 => 'SYR',
        /* Tadschikistan */
        215 => 'TJK',
        /* Taiwan (China) */
        216 => 'TWN',
        /* Tansania */
        217 => 'TZA',
        /* Thailand */
        218 => 'THA',
        /* Togo */
        219 => 'TGO',
        /* Tokelau */
        220 => 'TKL',
        /* Tonga */
        221 => 'TON',
        /* Trinidad und Tobago */
        222 => 'TTO',
        /* Tschad */
        223 => 'TCD',
        /* Tschechien */
        224 => 'CZE',
        /* Tunesien */
        225 => 'TUN',
        /* Türkei */
        226 => 'TUR',
        /* Turkmenistan */
        227 => 'TKM',
        /* Turks- und Caicosinseln */
        228 => 'TCA',
        /* Tuvalu */
        229 => 'TUV',
        /* Uganda */
        230 => 'UGA',
        /* Ukraine */
        231 => 'UKR',
        /* Ungarn */
        232 => 'HUN',
        /* Uruguay */
        233 => 'URY',
        /* Usbekistan */
        234 => 'UZB',
        /* Vanuatu */
        235 => 'VUT',
        /* Vatikanstadt */
        236 => 'VAT',
        /* Venezuela */
        237 => 'VEN',
        /* Vereinigte Arabische Emirate */
        238 => 'ARE',
        /* Vereinigte Staaten (USA) */
        239 => 'USA',
        /* Vereinigtes Königreich */
        240 => 'GBR',
        /* Vietnam */
        241 => 'VNM',
        /* Wallis und Futuna */
        242 => 'WLF',
        /* Weihnachtsinsel */
        243 => 'CXR',
        /* Westsahara */
        244 => 'ESH',
        /* Zaire */
        245 => 'ZAR',
        /* Zentralafrikanische Republik */
        246 => 'CAF',
        /* Zypern */
        247 => 'CYP',
        /* Serbien */
        251 => 'SRB',
        /* Kongo, Republik */
        252 => 'COG',
        /* Palästina */
        253 => 'PSE',
        /* Montenegro */
        254 => 'MNE',
        /* Südsudan */
        262 => 'SSD',
        /* Serbien und Montenegro */
        263 => 'SCG',
        /* Guernsey */
        264 => 'GGY',
        /* Jersey */
        265 => 'JEY',
        /* Isle of Man */
        266 => 'IMN',
        /* Sint Maarten */
        267 => 'SXM',
        /* Bonaire, Sint Eustatius, Saba */
        268 => 'BES',
        /* Saint-Martin */
        269 => 'MAF',
        /* Saint-Barthélemy */
        270 => 'BLM',
    ];

    /**
     * Map CO Ids to manual translations.
     * Since these don't map to countries that are part of ISO 3166-1.
     */
    private const SPECIAL_AREAS_MAPPING = [
        /* EU/EEA (without Austria) */
        -1 => [
            'de' => 'EU/EWR (ohne Österreich)',
            'en' => 'EU/EEA (without Austria)',
        ],
        /* Drittstaaten */
        -2 => [
            'de' => 'Drittstaaten',
            'en' => 'Third countries',
        ],
        /* Internat. Org. (außereurop.) */
        248 => [
            'de' => 'Internat. Org. (außereurop.)',
            'en' => 'International Organization (non-European)',
        ],
        /* Europäische Union */
        249 => [
            'de' => 'Europäische Union',
            'en' => 'European Union',
        ],
        /* Staatenlos */
        250 => [
            'de' => 'Staatenlos',
            'en' => 'Stateless',
        ],
        /* Italien (Südtirol) */
        255 => [
            'de' => 'Italien (Südtirol)',
            'en' => 'Italy (South Tyrol)',
        ],
        /* Staatsbürgerschaft ungeklärt */
        256 => [
            'de' => 'Staatsbürgerschaft ungeklärt',
            'en' => 'Citizenship Undetermined',
        ],
        /* Kosovo */
        // https://github.com/symfony/symfony/issues/54711
        257 => [
            'de' => 'Kosovo',
            'en' => 'Kosovo',
        ],
        /* Bophutatswana */
        258 => [
            'de' => 'Bophutatswana',
        ],
        /* Venda */
        259 => [
            'de' => 'Venda',
        ],
        /* Ciskei */
        260 => [
            'de' => 'Ciskei',
        ],
        /* Transkei */
        261 => [
            'de' => 'Transkei',
        ],
    ];

    public static function getAlpha3Code(int $coId): ?string
    {
        return self::COUNTRY_MAPPING_ALPHA3[$coId] ?? null;
    }

    /**
     * Returns the display name of a CO country ID for a given locale.
     */
    public static function getName(int $coId, string $locale, ?array $fallbackTranslations = null): string
    {
        // If it's ISO, ask the OS
        $alpha3Code = self::getAlpha3Code($coId);
        if ($alpha3Code !== null && Countries::alpha3CodeExists($alpha3Code)) {
            return Countries::getAlpha3Name($alpha3Code, $locale);
        }

        // If not, fall back to the manual translations mapping, or unknown
        return Utils::getTranslatedText(self::SPECIAL_AREAS_MAPPING, $coId, $locale, $fallbackTranslations);
    }
}
