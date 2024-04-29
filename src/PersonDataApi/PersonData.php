<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi;

class PersonData
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

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
        return new Nationality($this->data['NATIONALITYNR']);
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

        return ($id !== null) ? new Nationality($id) : null;
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
    public function getAdmissionQualificationTypeNumber(): string
    {
        return $this->data['ADMISSIONQUALIFICATIONTYPENR'];
    }

    /**
     * Examples:
     *   - "Bachelorstud. and. inl. Univ."
     *   - "H.techn.u.gewerbl. Lehranstalt"
     *   - "Realgymnasium"
     *   - "Studienberechtigungsprüfung"
     */
    public function getAdmissionQualificationType(): string
    {
        return $this->data['ADMISSIONQUALIFICATIONTYPE'];
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
    public function getHomeAddressCountry(): ?int
    {
        return $this->data['HOMEADDRESSCOUNTRY'];
    }

    /**
     * Example: "c/o Erika Mustermann".
     */
    public function getStudentAddressNote(): ?string
    {
        return $this->data['STUDADDRESSNOTE'];
    }

    /**
     * Example: "Hauptstraße 42/4".
     */
    public function getStudentAddressStreet(): ?string
    {
        return $this->data['STUDADDRESSSTREET'];
    }

    /**
     * Example: "Graz".
     */
    public function getStudentAddressPlace(): ?string
    {
        return $this->data['STUDADDRESSPLACE'];
    }

    /**
     * Example: "8010".
     */
    public function getStudentAddressPostCode(): ?string
    {
        return $this->data['STUDADDRESSPOSTCODE'];
    }

    /**
     * Example: 168.
     */
    public function getStudentAddressCountry(): ?int
    {
        return $this->data['STUDADDRESSCOUNTRY'];
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
        return PersonalStatus::from($this->data['PERSTUSTATUS']);
    }

    /**
     * One of (are there more?): "O", "M", "E", "A".
     *
     * Also called "Hörerstatus"
     */
    public function getStudentStatus(): StudentStatus
    {
        return StudentStatus::from($this->data['STUDTUSTATUS']);
    }

    /**
     * Example: "1970-01-01".
     */
    public function getImmatriculationDate(): string
    {
        return $this->getDateValue('IMMATRICULATIONDATE');
    }

    /**
     * Example: ??? Always null.
     */
    public function getExmatriculationStatus(): ?string
    {
        return $this->data['EXMATRICULATIONSTATUS'];
    }

    /**
     * Example: ??? Always null.
     */
    public function getExmatriculationDate(): ?string
    {
        return $this->getDateValue('EXMATRICULATIONDATE');
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
        return Gender::from($this->data['GENDER']);
    }
}
