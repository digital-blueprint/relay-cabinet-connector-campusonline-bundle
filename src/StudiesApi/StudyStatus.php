<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\StudiesApi;

enum StudyStatus: string
{
    /**
     * Studium offen (und Meldung im Vorsemester vorhanden).
     */
    case StudiesOpenWithPreviousRegistration = '#';

    /**
     * noch nicht gemeldet - Studienphase begonnen.
     */
    case NotYetRegisteredStudiesStarted = 'a';

    /**
     * gemeldet - Neueinschreibung.
     */
    case RegisteredNewEnrollment = 'B';

    /**
     * gemeldet - Ersteinschreibung.
     */
    case RegisteredFirstEnrollment = 'E';

    /**
     * gemeldet.
     */
    case Registered = 'I';

    /**
     * noch nicht gemeldet - Studium offen.
     */
    case NotYetRegisteredStudiesOpen = 'o';

    /**
     * Rücktritt von Meldung.
     */
    case WithdrawnFromRegistration = 'R';

    /**
     * beurlaubt.
     */
    case OnLeave = 'U';

    /**
     * Verzicht auf Studienplatz.
     */
    case RelinquishedUniversityPlace = 'V';

    /**
     * geschlossen (Abschluss u./o. keine Fortsetzung möglich).
     */
    case ClosedGraduationOrNoContinuation = 'X';

    /**
     * noch nicht gemeldet - fortzusetzen (erschwert zu öffnen).
     */
    case NotYetRegisteredPendingContinuationDifficult = 'y';

    /**
     * geschlossen (erschwert zu öffnen).
     */
    case ClosedDifficultToOpen = 'Y';

    /**
     * noch nicht gemeldet - fortzusetzen.
     */
    case NotYetRegisteredPendingContinuation = 'z';

    /**
     * geschlossen (Antrag oder ex lege).
     */
    case ClosedByApplicationOrLaw = 'Z';
}
