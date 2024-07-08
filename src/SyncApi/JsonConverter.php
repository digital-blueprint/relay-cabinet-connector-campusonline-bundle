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
        $studiesData = [];
        foreach ($studies as $study) {
            $entry = [
                'id' => $study->getStudyNumber(),
                'studentPersonNumber' => $study->getStudentPersonNumber(),
                'key' => $study->getStudyKey(),
                'type' => $study->getStudyType(),
                'name' => $study->getStudyName(),
                'semester' => $study->getStudySemester(),
                'status' => $study->getStudyStatus()->forJson(),
                'curriculumVersion' => $study->getStudyCurriculumVersion(),
                'immatriculationDate' => $study->getStudyImmatriculationDate(),
                'exmatriculationDate' => $study->getStudyExmatriculationDate(),
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
            $country = $application->getQualificationIssuingCountry();
            $entry = [
                'id' => $application->getApplicationNumber(),
                'studyId' => $application->getStudyNumber(),
                'studentPersonNumber' => $application->getStudentPersonNumber(),
                'applicationNumber' => $application->getApplicationNumber(),
                'studyKey' => $application->getStudyKey(),
                'studyName' => $application->getStudyName(),
                'studyType' => $application->getStudyType(),
                'startSemester' => $application->getStartSemester(),
                'qualificationCertificateDate' => $application->getQualificationCertificateDate(),
                'qualificationIssuingCountry' => $country?->forJson(),
                'qualificationType' => $application->getQualification()?->forJson(),
            ];
            $applicationsData[] = $entry;
        }

        $data = [
            'id' => $student->getIdentNumberObfuscated(),
            'givenName' => $student->getGivenName(),
            'familyName' => $student->getFamilyName(),
            'studentPersonNumber' => (string) $student->getStudentPersonNumber(),
            'gender' => $student->getGender()->forJson(),
            'studentStatus' => $student->getStudentStatus()->forJson(),
            'studies' => $studiesData,
            'applications' => $applicationsData,
            'nationality' => $student->getNationality()->forJson(),
            'exmatriculationStatus' => $student->getExmatriculationStatus()?->forJson(),
        ];

        return $data;
    }
}
