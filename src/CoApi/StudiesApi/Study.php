<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseResource;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Country;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ExmatriculationStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\HigherEducationEntranceQualification;

class Study extends BaseResource
{
    private function getDateValue(string $name): ?string
    {
        $obj = $this->data[$name] ?? null;
        assert($obj !== null);

        return $obj['value'];
    }

    /**
     * Example: 252221.
     */
    public function getStudyNumber(): int
    {
        return $this->data['STSTUDIUMNR'];
    }

    /**
     * Example: 123.
     */
    public function getStudentPersonNumber(): int
    {
        return $this->data['STPERSONNR'];
    }

    /**
     * Example: "UF 786 600".
     */
    public function getStudyKey(): string
    {
        return $this->data['STUDYKEY'];
    }

    /**
     * Example: "Doktoratsstudium".
     */
    public function getStudyType(): string
    {
        return $this->data['STUDYTYPE'];
    }

    /**
     * Example: "Dr.-Studium d.technischen Wissenschaften; Architektur".
     */
    public function getStudyName(): string
    {
        return $this->data['STUDYNAME'];
    }

    /**
     * Example: 28.
     */
    public function getStudySemester(): int
    {
        return $this->data['STUDYSEMESTER'];
    }

    /**
     * Examples: "I", "B", "U", "o", "E", ...
     */
    public function getStudyStatus(): StudyStatus
    {
        return StudyStatus::from($this->data['STUDYSTATUS']);
    }

    /**
     * Example: "12U_SPO".
     */
    public function getStudyCurriculumVersion(): ?string
    {
        return $this->data['STUDYCURRICULUMVERSION'];
    }

    /**
     * Example: "2010-01-01".
     */
    public function getStudyImmatriculationDate(): string
    {
        $value = $this->getDateValue('STUDYIMMATRICULATIONDATE');
        // From what I see this is always set, unlike the other dates
        assert($value !== null);

        return $value;
    }

    /**
     * Example: "2010-01-01".
     */
    public function getStudyExmatriculationDate(): ?string
    {
        return $this->getDateValue('STUDYEXMATRICULATIONDATE');
    }

    /**
     * Example: "41".
     */
    public function getStudyQualificationType(): ?HigherEducationEntranceQualification
    {
        $id = $this->data['STUDYQUALIFICATIONTYPE'];

        return $id !== null ? HigherEducationEntranceQualification::fromId($id) : null;
    }

    /**
     * Example: "2010-01-01".
     */
    public function getStudyQualificationDate(): ?string
    {
        return $this->getDateValue('STUDYQUALIFICATIONDATE');
    }

    /**
     * Example: "168".
     */
    public function getStudyQualificationState(): ?Country
    {
        $coId = $this->data['STUDYQUALIFICATIONSTATENR'];

        return $coId !== null ? Country::fromId($coId) : null;
    }

    /**
     * Example: "Österreich".
     */
    public function getStudyQualificationStateString(): ?string
    {
        return $this->data['STUDYQUALIFICATIONSTATE'];
    }

    /**
     * Example: "EZ".
     */
    public function getStudyExmatriculationType(): ?ExmatriculationStatus
    {
        $coId = $this->data['STUDYEXMATRICULATIONTYPE'];

        return $coId !== null ? ExmatriculationStatus::fromId($coId) : null;
    }

    /**
     * TODO: ???
     */
    public function getAdditionalCertificate(): ?string
    {
        return $this->data['ADDITIONALCERTIFICATE'];
    }
}
