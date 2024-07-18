<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\SyncApi;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\ApplicationsApi\Application;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudentsApi\Student;
use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\StudiesApi\Study;

class JsonConverter
{
    /**
     * @param Study[]       $studies
     * @param Application[] $applications
     */
    public static function convertToJsonObject(Student $student, array $studies, array $applications): array
    {
        $syncDateTime = null;

        $studiesData = [];
        foreach ($studies as $study) {
            Utils::updateMinSyncDateTime($study, $syncDateTime);
            $entry = [
                'id' => $study->getStudyNumber(),
                'webUrl' => $study->getWebUrl(),
                'studentPersonNumber' => $study->getStudentPersonNumber(),
                'key' => $study->getStudyKey(),
                'type' => $study->getStudyType(),
                'name' => $study->getStudyName(),
                'semester' => $study->getStudySemester(),
                'status' => $study->getStudyStatus()->forJson(),
                'curriculumVersion' => $study->getStudyCurriculumVersion(),
                'immatriculationDate' => $study->getStudyImmatriculationDate(),
                'immatriculationSemester' => $study->getStudyImmatriculationSemester(),
                'exmatriculationDate' => $study->getStudyExmatriculationDate(),
                'exmatriculationSemester' => $study->getStudyExmatriculationSemester(),
                'qualificationType' => $study->getStudyQualificationType()?->forJson(),
                'qualificationDate' => $study->getStudyQualificationDate(),
                'qualificationState' => $study->getStudyQualificationState()?->forJson(),
                'exmatriculationType' => $study->getStudyExmatriculationType()?->forJson(),
                'additionalCertificates' => $study->getAdditionalCertificate()?->forJson(),
            ];
            $studiesData[] = $entry;
        }

        $applicationsData = [];
        foreach ($applications as $application) {
            Utils::updateMinSyncDateTime($application, $syncDateTime);
            $entry = [
                'id' => $application->getApplicationNumber(),
                'studyId' => $application->getStudyNumber(),
                'studentPersonNumber' => $application->getStudentPersonNumber(),
                'studyKey' => $application->getStudyKey(),
                'studyName' => $application->getStudyName(),
                'studyType' => $application->getStudyType(),
                'startSemester' => $application->getStartSemester(),
                'qualificationCertificateDate' => $application->getQualificationCertificateDate(),
                'qualificationIssuingCountry' => $application->getQualificationIssuingCountry()?->forJson(),
                'qualificationType' => $application->getQualification()?->forJson(),
            ];
            $applicationsData[] = $entry;
        }

        // We take the oldest sync time of all contained objects as the last sync time (worst case).
        // This means the returned data is at least newer then the given date time.
        Utils::updateMinSyncDateTime($student, $syncDateTime);
        $syncDateTimeString = $syncDateTime->format(\DateTimeInterface::ATOM);

        $data = [
            'id' => $student->getIdentNumberObfuscated(),
            'webUrl' => $student->getWebUrl(),
            'syncDateTime' => $syncDateTimeString,
            'studentId' => $student->getStudentId(),
            'givenName' => $student->getGivenName(),
            'familyName' => $student->getFamilyName(),
            'birthDate' => $student->getBirthDate(),
            'studentPersonNumber' => (string) $student->getStudentPersonNumber(),
            'studies' => $studiesData,
            'applications' => $applicationsData,
            'nationality' => $student->getNationality()->forJson(),
            'nationalitySecondary' => $student->getNationalitySecondary()?->forJson(),
            'admissionQualificationType' => $student->getAdmissionQualificationType()->forJson(),
            'admissionQualificationState' => $student->getAdmissionQualificationState()?->forJson(),
            'telephoneNumber' => $student->getTelephoneNumber(),
            'schoolCertificateDate' => $student->getSchoolCertificateDate(),
            'homeAddressNote' => $student->getHomeAddressNote(),
            'homeAddressStreet' => $student->getHomeAddressStreet(),
            'homeAddressPlace' => $student->getHomeAddressPlace(),
            'homeAddressPostCode' => $student->getHomeAddressPostCode(),
            'homeAddressCountry' => $student->getHomeAddressCountry()?->forJson(),
            'homeAddressTelephoneNumber' => $student->getHomeAddressTelephoneNumber(),
            'studentAddressNote' => $student->getStudentAddressNote(),
            'studentAddressStreet' => $student->getStudentAddressStreet(),
            'studentAddressPlace' => $student->getStudentAddressPlace(),
            'studentAddressPostCode' => $student->getStudentAddressPostCode(),
            'studentAddressCountry' => $student->getStudentAddressCountry()?->forJson(),
            'studentAddressTelephoneNumber' => $student->getStudentAddressTelephoneNumber(),
            'emailAddressUniversity' => $student->getEmailAddressUniversity(),
            'emailAddressConfirmed' => $student->getEmailAddressConfirmed(),
            'emailAddressTemporary' => $student->getEmailAddressTemporary(),
            'personalStatus' => $student->getPersonalStatus()->forJson(),
            'studentStatus' => $student->getStudentStatus()->forJson(),
            'tuitionStatus' => $student->getTuitionStatus(),
            'tuitionExemptionType' => $student->getTuitionExemptionType(),
            'immatriculationDate' => $student->getImmatriculationDate(),
            'exmatriculationStatus' => $student->getExmatriculationStatus()?->forJson(),
            'immatriculationSemester' => $student->getImmatriculationSemester(),
            'exmatriculationDate' => $student->getExmatriculationDate(),
            'termStart' => $student->getTermStart(),
            'termEnd' => $student->getTermEnd(),
            'academicTitlePreceding' => $student->getAcademicTitlePreceding(),
            'academicTitleFollowing' => $student->getAcademicTitleFollowing(),
            'formerFamilyName' => $student->getFormerFamilyName(),
            'socialSecurityNumber' => $student->getSocialSecurityNumber(),
            'sectorSpecificPersonalIdentifier' => $student->getSectorSpecificPersonalIdentifier(),
            'gender' => $student->getGender()->forJson(),
            'note' => $student->getNote(),
        ];

        return $data;
    }
}
