<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

/**
 * Additional/Supplementary exams.
 */
class AdditionalExams
{
    /**
     * @var string[]
     */
    public array $values;

    public function __construct(?string $value)
    {
        $this->values = $value !== null ? explode(' | ', $value) : [];
    }

    public function forJson(): array
    {
        $res = [];

        foreach ($this->values as $value) {
            $res[] = [
                'key' => $value,
                'translations' => [
                    // FIXME
                    'de' => $value,
                    'en' => $value,
                ],
            ];
        }

        return $res;
    }
}
