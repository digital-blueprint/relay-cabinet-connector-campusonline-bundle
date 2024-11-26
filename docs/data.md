# Provided Student Data

The CO connector returns the following data structure. One entry is one student
with embedded studies and applications. Object fields are always present, but
can be null if the value is not available. Array fields are always present, but
can be empty.

Dummy example response with all fields present (the values are all made up):

There is currently no documentation on the meaning of the fields and the
possible values. Please see the source for more info.

```json
{
  "academicTitleFollowing": "Bsc",
  "academicTitlePreceding": "Ing.",
  "admissionQualificationState": {
    "alpha3Code": "BIH",
    "key": "40",
    "translations": {
      "de": "Bosnien und Herzegowina",
      "en": "Bosnia & Herzegovina"
    }
  },
  "admissionQualificationType": {
    "key": "02",
    "translations": {
      "de": "Humanistisches Gymnasium",
      "en": "Humanistisches Gymnasium"
    }
  },
  "applications": [
    {
      "id": "12345",
      "qualificationCertificateDate": "1970-01-01",
      "qualificationIssuingCountry": {
        "alpha3Code": "AUT",
        "key": "168",
        "translations": {
          "de": "Österreich",
          "en": "Austria"
        }
      },
      "qualificationType": {
        "key": "25",
        "translations": {
          "de": "ausländische Reifeprüfung",
          "en": "foreign secondary school leaving exam"
        }
      },
      "startSemester": "21W",
      "studentPersonId": "123456",
      "studyId": "253324",
      "studyKey": "UF 033 678",
      "studyName": "Bachelorstudium; Physik",
      "studyType": "Bachelorstudium"
    }
  ],
  "birthDate": "1970-01-01",
  "emailAddressConfirmed": "max.mustermann@example.com",
  "emailAddressTemporary": "max.mustermann.temp@example.com",
  "emailAddressUniversity": "max.mustermann@student.tugraz.at",
  "exmatriculationDate": "2023-10-31",
  "exmatriculationSemester": "23W",
  "exmatriculationStatus": {
    "key": "EZ",
    "translations": {
      "de": "ex lege (EZ)",
      "en": "ex lege (EZ)"
    }
  },
  "familyName": "Mustermann",
  "formerFamilyName": "Normalverbraucher",
  "gender": {
    "key": "M",
    "translations": {
      "de": "Männlich",
      "en": "male"
    }
  },
  "givenName": "Max",
  "homeAddressCountry": {
    "alpha3Code": "EGY",
    "key": "11",
    "translations": {
      "de": "Ägypten",
      "en": "Egypt"
    }
  },
  "homeAddressNote": "c/o Erika Mustermann",
  "homeAddressPlace": "Altenmarkt bei Sankt Gallen",
  "homeAddressPostCode": "8934",
  "homeAddressRegion": "Wien",
  "homeAddressStreet": "Hauptstraße 34",
  "homeAddressTelephoneNumber": "067612345678",
  "id": "123123",
  "identNumberObfuscated": "F06BCC80D6FC0BDE575B16FB2E3790D5",
  "immatriculationDate": "2010-12-24",
  "immatriculationSemester": "21W",
  "nationality": {
    "alpha3Code": "EGY",
    "key": "11",
    "translations": {
      "de": "Ägypten",
      "en": "Egypt"
    }
  },
  "nationalitySecondary": {
    "alpha3Code": "AUT",
    "key": "168",
    "translations": {
      "de": "Österreich",
      "en": "Austria"
    }
  },
  "note": "some note",
  "personalStatus": {
    "key": "Voranmeldung",
    "translations": {
      "de": "Voranmeldung",
      "en": "pre-registration"
    }
  },
  "schoolCertificateDate": "2010-12-24",
  "sectorSpecificPersonalIdentifier": "Kxl/ufp/HOufd8y/+3n6qZ1Cn7E=",
  "socialSecurityNumber": "1223010170",
  "studentId": "00712345",
  "studentStatus": {
    "key": "E",
    "translations": {
      "de": "nicht zugelassen",
      "en": "not admitted"
    }
  },
  "studies": [
    {
      "additionalCertificates": [
        {
          "key": "EDG",
          "translations": {
            "de": "Erg.Prfg. - Darstellende Geometrie",
            "en": "suppl.exam. - Descriptive Geometry"
          }
        },
        {
          "key": "EGR",
          "translations": {
            "de": "Erg.Prfg. - Griechisch",
            "en": "suppl.exam. - Greek"
          }
        }
      ],
      "curriculumVersion": "2022W",
      "exmatriculationDate": "2022-01-01",
      "exmatriculationSemester": "24S",
      "exmatriculationType": {
        "key": "EZ",
        "translations": {
          "de": "ex lege (EZ)",
          "en": "ex lege (EZ)"
        }
      },
      "id": "253324",
      "immatriculationDate": "2021-01-01",
      "immatriculationSemester": "20S",
      "key": "UF 066 921",
      "name": "Masterstudium; Computer Science",
      "qualificationDate": "2010-01-01",
      "qualificationState": {
        "alpha3Code": "AUT",
        "key": "168",
        "translations": {
          "de": "Österreich",
          "en": "Austria"
        }
      },
      "qualificationType": {
        "key": "41",
        "translations": {
          "de": "Master-/Diplomst.eigene Univ.",
          "en": "Master/ Diploma study programme at own university"
        }
      },
      "semester": 6,
      "status": {
        "key": "I",
        "translations": {
          "de": "gemeldet",
          "en": "gemeldet"
        }
      },
      "studentPersonId": "123456",
      "type": "Masterstudium",
      "webUrl": "https://dummy.at/dummy/wbStmStudiendaten.wbStudiendetails?pStPersonNr=123456&pStStudiumNr=253324"
    }
  ],
  "studyAddressCountry": {
    "alpha3Code": "EGY",
    "key": "11",
    "translations": {
      "de": "Ägypten",
      "en": "Egypt"
    }
  },
  "studyAddressNote": "c/o Erika Mustermann",
  "studyAddressPlace": "Waizenkirchen",
  "studyAddressPostCode": "4730",
  "studyAddressRegion": "Steiermark",
  "studyAddressStreet": "Hauptstraße 42",
  "studyAddressTelephoneNumber": "067612345676",
  "studyLimitEndSemester": "24S",
  "studyLimitStartSemester": "23W",
  "syncDateTime": "2024-06-13T11:01:15+00:00",
  "telephoneNumber": "067612345677",
  "tuitionExemptionType": "L Lehrgang",
  "tuitionStatus": "Ausländer gleichgestellt",
  "webUrl": "https://dummy.at/dummy/wbStEvidenz.StEvi?pStPersonNr=123123"
}
```
