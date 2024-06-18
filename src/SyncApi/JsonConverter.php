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
                'key' => $study->getStudyKey(),
                'name' => $study->getStudyName(),
                'type' => $study->getStudyType(),
                'curriculumVersion' => $study->getStudyCurriculumVersion(),
            ];
            $studiesData[] = $entry;
        }

        $applicationsData = [];
        foreach ($applications as $application) {
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
                'qualificationIssuingCountry' => $application->getQualificationIssuingCountry()->forJson(),
                'qualificationType' => $application->getQualification()->forJson(),
            ];
            $applicationsData[] = $entry;
        }

        $exmatriculationStatus = $student->getExmatriculationStatus();
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
            'exmatriculationStatus' => $exmatriculationStatus !== null ? $exmatriculationStatus->forJson() : null,
        ];

        return $data;
    }
}
