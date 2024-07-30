<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseResource;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Country;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ExmatriculationStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\HigherEducationEntranceQualification;
use League\Uri\UriTemplate;

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
        return new StudyStatus($this->data['STUDYSTATUSKEY'], ['de' => $this->getStudyStatusString()]);
    }

    /**
     * Example: "geschlossen (Antrag oder ex lege)".
     */
    public function getStudyStatusString(): string
    {
        return $this->data['STUDYSTATUS'];
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
    public function getStudyImmatriculationDate(): ?string
    {
        return $this->getDateValue('STUDYIMMATRICULATIONDATE');
    }

    /**
     * Example: "20S".
     */
    public function getStudyImmatriculationSemester(): ?string
    {
        return $this->data['STUDYIMMATRICULATIONSEMESTER'];
    }

    /**
     * Example: "2010-01-01".
     */
    public function getStudyExmatriculationDate(): ?string
    {
        return $this->getDateValue('STUDYEXMATRICULATIONDATE');
    }

    /**
     * Whether the record is considered "active". This mirrors the rules of the CO query.
     */
    public function isActive(): bool
    {
        return $this->getStudyExmatriculationDate() !== null;
    }

    /**
     * Example: "24S".
     */
    public function getStudyExmatriculationSemester(): ?string
    {
        return $this->data['STUDYEXMATRICULATIONSEMESTER'];
    }

    /**
     * Example: "41".
     */
    public function getStudyQualificationType(): ?HigherEducationEntranceQualification
    {
        $id = $this->data['STUDYQUALIFICATIONTYPENR'];

        return $id !== null ? new HigherEducationEntranceQualification($id, ['de' => $this->getStudyQualificationTypeString()]) : null;
    }

    /**
     * Example: "Master-/Diplomst.eigene Univ.".
     */
    public function getStudyQualificationTypeString(): ?string
    {
        return $this->data['STUDYQUALIFICATIONTYPE'];
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

        return $coId !== null ? new Country($coId, ['de' => $this->getStudyQualificationStateString()]) : null;
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
        $coId = $this->data['STUDYEXMATRICULATIONTYPEKEY'];

        return $coId !== null ? new ExmatriculationStatus($coId, ['de' => $this->getStudyExmatriculationTypeString()]) : null;
    }

    /**
     * Example: "auf Antrag".
     */
    public function getStudyExmatriculationTypeString(): ?string
    {
        return $this->data['STUDYEXMATRICULATIONTYPE'];
    }

    /**
     * Examples: 'EDG', 'ED', 'ZDG', 'EDG | EGR', 'EBE', 'EDG | EDG', 'AZP', 'EL', 'EBU', 'ZBU'.
     */
    public function getAdditionalCertificates(): AdditionalExams
    {
        $value = $this->data['ADDITIONALCERTIFICATE'];

        return new AdditionalExams($value);
    }

    /**
     * Returns a URL to a website for displaying and editing the source study data.
     */
    public function getWebUrl(): string
    {
        $baseUrl = $this->baseApi->getBaseUrl();
        $uriTemplate = new UriTemplate(rtrim($baseUrl, '/').'/wbStmStudiendaten.wbStudiendetails{?pStPersonNr,pStStudiumNr}');

        return (string) $uriTemplate->expand([
            'pStPersonNr' => $this->getStudentPersonNumber(),
            'pStStudiumNr' => $this->getStudyNumber(),
        ]);
    }
}
