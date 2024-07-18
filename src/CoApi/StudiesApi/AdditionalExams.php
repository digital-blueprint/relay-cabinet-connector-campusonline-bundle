<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

/**
 * Additional/Supplementary exams.
 */
class AdditionalExams
{
    /**
     * @var AdditionalExam[]
     */
    public array $items;

    public function __construct(?string $value)
    {
        $parts = $value !== null ? explode(' | ', $value) : [];
        $this->items = [];
        foreach ($parts as $part) {
            $this->items[] = new AdditionalExam($part);
        }
    }

    public function forJson(): array
    {
        $res = [];
        foreach ($this->items as $item) {
            $res[] = $item->forJson();
        }

        return $res;
    }
}
