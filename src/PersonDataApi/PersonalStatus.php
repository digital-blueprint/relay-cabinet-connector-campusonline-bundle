<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\PersonDataApi;

enum PersonalStatus: string
{
    case EnrollmentOpen = 'Einschreibung offen';

    case ExternalPerson = 'externe Person';

    case ValidStudent = 'gültige/r Studierende/r';

    case UniversityEntranceExam = 'Studienberechtigungsprüfung';

    case TestData = 'Testdaten';

    case PreRegistration = 'Voranmeldung';

    case PreRegistrationWithoutUniversityStatistics = 'Voranmeldung ohne Hochschulstatistik';
}
