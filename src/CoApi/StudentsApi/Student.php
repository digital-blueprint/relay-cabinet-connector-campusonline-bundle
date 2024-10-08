<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\BaseResource;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\Country;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ExmatriculationStatus;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\HigherEducationEntranceQualification;
use League\Uri\UriTemplate;

class Student extends BaseResource
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
     * Example: "01020340".
     *
     * Also called "Identifikationsnummer"
     */
    public function getStudentId(): ?string
    {
        return $this->data['STUDID'];
    }

    /**
     * Example: "Max".
     */
    public function getGivenName(): string
    {
        return $this->data['GIVENNAME'];
    }

    /**
     * Example: "Mustermann".
     */
    public function getFamilyName(): string
    {
        return $this->data['FAMILYNAME'];
    }

    /**
     * Example: "1970-01-01".
     */
    public function getBirthDate(): string
    {
        return $this->getDateValue('BIRTHDATE');
    }

    /**
     * Example: "F06BCC80D6FC0BDE575B16FB2E3790D5".
     */
    public function getIdentNumberObfuscated(): string
    {
        return $this->data['IDENTNROBFUSCATED'];
    }

    /**
     * Example: 168.
     */
    public function getNationality(): Nationality
    {
        return new Nationality($this->data['NATIONALITYNR'], ['de' => $this->getNationalityString()]);
    }

    /**
     * Example: "Österreich".
     */
    public function getNationalityString(): string
    {
        return $this->data['NATIONALITY'];
    }

    /**
     * Example: 168.
     */
    public function getNationalitySecondary(): ?Nationality
    {
        $id = $this->data['NATIONALITYSECONDARYNR'];

        return ($id !== null) ? new Nationality($id, ['de' => $this->getNationalitySecondaryString()]) : null;
    }

    /**
     * Example: "Österreich".
     */
    public function getNationalitySecondaryString(): ?string
    {
        return $this->data['NATIONALITYSECONDARY'];
    }

    /**
     * Example: "38".
     */
    public function getAdmissionQualificationType(): HigherEducationEntranceQualification
    {
        return new HigherEducationEntranceQualification($this->data['ADMISSIONQUALIFICATIONTYPENR'], ['de' => $this->getAdmissionQualificationTypeString()]);
    }

    /**
     * Examples:
     *   - "Bachelorstud. and. inl. Univ."
     *   - "H.techn.u.gewerbl. Lehranstalt"
     *   - "Realgymnasium"
     *   - "Studienberechtigungsprüfung"
     */
    public function getAdmissionQualificationTypeString(): string
    {
        return $this->data['ADMISSIONQUALIFICATIONTYPE'];
    }

    /**
     * Example: 40.
     */
    public function getAdmissionQualificationState(): ?Country
    {
        $coId = $this->data['ADMISSIONQUALIFICATIONSTATENR'];

        return $coId !== null ? new Country($coId, ['de' => $this->getAdmissionQualificationStateString()]) : null;
    }

    /**
     * Example: "Bosnien und Herzegowina".
     */
    public function getAdmissionQualificationStateString(): ?string
    {
        return $this->data['ADMISSIONQUALIFICATIONSTATE'];
    }

    /**
     * Example: "067612345678".
     */
    public function getTelephoneNumber(): ?string
    {
        return $this->data['TELEPHONE'];
    }

    /**
     * Example: "1970-01-01".
     */
    public function getSchoolCertificateDate(): ?string
    {
        return $this->getDateValue('SCHOOLCERTIFICATEDATE');
    }

    /**
     * Example: "c/o Erika Mustermann".
     */
    public function getHomeAddressNote(): ?string
    {
        return $this->data['HOMEADDRESSNOTE'];
    }

    /**
     * Example: "Steiermark".
     */
    public function getHomeAddressRegion(): ?string
    {
        return $this->data['HOMEADDRESSREGION'];
    }

    /**
     * Example: "Hauptstraße 42/4".
     */
    public function getHomeAddressStreet(): ?string
    {
        return $this->data['HOMEADDRESSSTREET'];
    }

    /**
     * Example: "Graz".
     */
    public function getHomeAddressPlace(): ?string
    {
        return $this->data['HOMEADDRESSPLACE'];
    }

    /**
     * Example: "8010".
     */
    public function getHomeAddressPostCode(): ?string
    {
        return $this->data['HOMEADDRESSPOSTCODE'];
    }

    /**
     * Example: 168.
     */
    public function getHomeAddressCountry(): ?Country
    {
        $coId = $this->data['HOMEADDRESSCOUNTRYNR'];

        return $coId !== null ? new Country($coId, ['de' => $this->getHomeAddressCountryString()]) : null;
    }

    /**
     * Example: "Österreich".
     */
    public function getHomeAddressCountryString(): ?string
    {
        return $this->data['HOMEADDRESSCOUNTRY'];
    }

    /**
     * Example: "067612345678".
     */
    public function getHomeAddressTelephoneNumber(): ?string
    {
        return $this->data['HOMEADDRESSTELEPHONE'];
    }

    /**
     * Example: "c/o Erika Mustermann".
     */
    public function getStudyAddressNote(): ?string
    {
        return $this->data['STUDADDRESSNOTE'];
    }

    /**
     * Example: "Steiermark".
     */
    public function getStudyAddressRegion(): ?string
    {
        return $this->data['STUDADDRESSREGION'];
    }

    /**
     * Example: "Hauptstraße 42/4".
     */
    public function getStudyAddressStreet(): ?string
    {
        return $this->data['STUDADDRESSSTREET'];
    }

    /**
     * Example: "Graz".
     */
    public function getStudyAddressPlace(): ?string
    {
        return $this->data['STUDADDRESSPLACE'];
    }

    /**
     * Example: "8010".
     */
    public function getStudyAddressPostCode(): ?string
    {
        return $this->data['STUDADDRESSPOSTCODE'];
    }

    /**
     * Example: 168.
     */
    public function getStudyAddressCountry(): ?Country
    {
        $coId = $this->data['STUDADDRESSCOUNTRYNR'];

        return $coId !== null ? new Country($coId, ['de' => $this->getStudyAddressCountryString()]) : null;
    }

    /**
     * Example: "Österreich".
     */
    public function getStudyAddressCountryString(): ?string
    {
        return $this->data['STUDADDRESSCOUNTRY'];
    }

    /**
     * Example: "067612345678".
     */
    public function getStudyAddressTelephoneNumber(): ?string
    {
        return $this->data['STUDADDRESSTELEPHONE'];
    }

    /**
     * Example: max.mustermann@student.tugraz.at.
     */
    public function getEmailAddressUniversity(): ?string
    {
        return $this->data['EMAILADDRESSTU'];
    }

    /**
     * Example: max.mustermann@example.com.
     */
    public function getEmailAddressConfirmed(): ?string
    {
        return $this->data['EMAILADDRESSCONFIRMED'];
    }

    /**
     * Example: "max.mustermann@example.com".
     */
    public function getEmailAddressTemporary(): ?string
    {
        return $this->data['EMAILADDRESSTEMPORARY'];
    }

    /**
     * One of (are there more?):
     *   - "Testdaten"
     *   - "gültige/r Studierende/r"
     *   - "Studienberechtigungsprüfung"
     *   - "Voranmeldung"
     *   - "Einschreibung offen"
     */
    public function getPersonalStatus(): PersonalStatus
    {
        return new PersonalStatus($this->data['PERSSTATUS']);
    }

    /**
     * One of (are there more?): "O", "M", "E", "A".
     *
     * Also called "Hörerstatus"
     */
    public function getStudentStatus(): StudentStatus
    {
        return new StudentStatus($this->data['STUDSTATUSKEY'], ['de' => $this->getStudentStatusString()]);
    }

    /**
     * Example: "nicht zugelassen".
     *
     * Also called "Hörerstatus"
     */
    public function getStudentStatusString(): string
    {
        return $this->data['STUDSTATUS'];
    }

    /**
     * Example: "Ausländer gleichgestellt".
     *
     * Also called "Beitragsstatus"
     */
    public function getTuitionStatus(): ?string
    {
        return $this->data['TUITIONSTATUS'];
    }

    /**
     * Example: "L Lehrgang".
     *
     * Also called "Befreiungsart"
     */
    public function getTuitionExemptionType(): ?string
    {
        return $this->data['TUITIONEXEMPTIONTYPE'];
    }

    /**
     * Example: "1970-01-01".
     */
    public function getImmatriculationDate(): string
    {
        return $this->getDateValue('IMMATRICULATIONDATE');
    }

    /**
     * Example: "EZ".
     */
    public function getExmatriculationStatus(): ?ExmatriculationStatus
    {
        $coId = $this->data['EXMATRICULATIONSTATUSKEY'];

        return $coId !== null ? new ExmatriculationStatus($coId, ['de' => $this->getExmatriculationStatusString()]) : null;
    }

    /**
     * Example: "ex lege".
     */
    public function getExmatriculationStatusString(): ?string
    {
        return $this->data['EXMATRICULATIONSTATUS'];
    }

    /**
     * Example: "22W".
     */
    public function getImmatriculationSemester(): ?string
    {
        return $this->data['IMMATRICULATIONSEMESTER'];
    }

    /**
     * Example: "2023-10-31".
     */
    public function getExmatriculationDate(): ?string
    {
        return $this->getDateValue('EXMATRICULATIONDATE');
    }

    /**
     * Example: "22W".
     */
    public function getExmatriculationSemester(): ?string
    {
        return $this->data['EXMATRICULATIONSEMESTER'];
    }

    /**
     * Example: "23W".
     *
     * Also called "Befristet von"
     */
    public function getStudyLimitStartSemester(): ?string
    {
        return $this->data['TERMSTART'];
    }

    /**
     * Example: "24S".
     *
     * Also called "Befristet bis"
     */
    public function getStudyLimitEndSemester(): ?string
    {
        return $this->data['TERMEND'];
    }

    /**
     * Examples (looks freeform):
     *   - "Ing."
     *   - "Dipl.-Ing. Dr.techn."
     *   - "Mag.iur. Dipl.-Ing."
     *   - "Dipl.-Ing.Dr.techn."
     */
    public function getAcademicTitlePreceding(): ?string
    {
        return $this->data['ACADEMICTITLEPRECEDING'];
    }

    /**
     * Examples (looks freeform):
     *   - "Bsc"
     *   - "Bakk.techn."
     *   - "Bakk.rer.soc.oec."
     *   - "MEng"
     */
    public function getAcademicTitleFollowing(): ?string
    {
        return $this->data['ACADEMICTITLEFOLLOWING'];
    }

    /**
     * Example: "Normalverbraucher".
     */
    public function getFormerFamilyName(): ?string
    {
        return $this->data['FORMERFAMILYNAME'];
    }

    /**
     * Example: "1234010197".
     */
    public function getSocialSecurityNumber(): ?string
    {
        return $this->data['SOCIALSECURITYNR'];
    }

    /**
     * Example: "Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=".
     */
    public function getSectorSpecificPersonalIdentifier(): ?string
    {
        return $this->data['BPK'];
    }

    /**
     * One of "M", "W", "X", "U", "O", "I", "K".
     */
    public function getGender(): Gender
    {
        return new Gender($this->data['GENDERKEY'], ['de' => $this->getGenderString()]);
    }

    /**
     * Example: "Weiblich".
     */
    public function getGenderString(): string
    {
        return $this->data['GENDER'];
    }

    /**
     * ???
     */
    public function getNote(): ?string
    {
        return $this->data['NOTE'];
    }

    /**
     * Whether the record is considered "active". This mirrors the rules of the CO query.
     */
    public function isActive(): bool
    {
        return $this->getExmatriculationDate() !== null;
    }

    /**
     * Returns a URL to a website for displaying and editing the source student data.
     */
    public function getWebUrl(): string
    {
        $baseUrl = $this->baseApi->getBaseUrl();
        $uriTemplate = new UriTemplate(rtrim($baseUrl, '/').'/wbStEvidenz.StEvi{?pStPersonNr}');

        return (string) $uriTemplate->expand([
            'pStPersonNr' => $this->getStudentPersonNumber(),
        ]);
    }
}
