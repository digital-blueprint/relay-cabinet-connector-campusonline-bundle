<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseResource;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Country;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\HigherEducationEntranceQualification;

class Application extends BaseResource
{
    private function getDateValue(string $name): ?string
    {
        $obj = $this->data[$name] ?? null;
        assert($obj !== null);

        return $obj['value'];
    }

    /**
     * Example: 123.
     */
    public function getStudentPersonNumber(): int
    {
        return $this->data['STPERSONNR'];
    }

    /**
     * Example: "25 - ausländische Reifeprüfung".
     */
    public function getQualification(): ?HigherEducationEntranceQualification
    {
        $value = $this->data['APPLICANTQUALIFICATIONTYPE'];

        return $value !== null ? HigherEducationEntranceQualification::fromDisplayText($value, 'de') : null;
    }

    /**
     * Example: 40.
     */
    public function getQualificationIssuingCountry(): ?Country
    {
        $coId = $this->data['APPLICANTQUALIFICATIONSTATENR'];

        return $coId !== null ? new Country($coId) : null;
    }

    /**
     * Example: "Bosnien und Herzegowina".
     */
    public function getQualificationIssuingCountryString(): string
    {
        return $this->data['APPLICANTQUALIFICATIONSTATE'];
    }

    /**
     * Example: "2020-06-29".
     */
    public function getQualificationCertificateDate(): ?string
    {
        return $this->getDateValue('APPLICANTQUALIFICATIONDATE');
    }

    /**
     * Example: "22W".
     */
    public function getStartSemester(): string
    {
        return $this->data['APPLICANTSTARTOFSTUDY'];
    }

    /**
     * Example: "UF 992 840".
     */
    public function getStudyKey(): string
    {
        return $this->data['APPLICANTSTUDYKEY'];
    }

    /**
     * Example: "Bachelorstudium; Physik".
     */
    public function getStudyName(): string
    {
        return $this->data['APPLICANTSTUDYNAME'];
    }

    /**
     * Example: "Doktoratsstudium" or "Bachelorstudium".
     */
    public function getStudyType(): string
    {
        return $this->data['APPLICANTSTUDYTYPE'];
    }

    /**
     * Example: 252221.
     */
    public function getStudyNumber(): ?int
    {
        return $this->data['STSTUDIUMNR'];
    }

    /**
     * Example: 30204.
     */
    public function getApplicationNumber(): int
    {
        return $this->data['BEWERBUNGNR'];
    }
}
