# Changelog

## v0.3.1

* New dbp:relay:cabinet-connector-campusonline:sync command which fetches all data (only
  for testing for now)
* Added a new config option `exclude_inactive` which allows to limit the sync to active records
* Added a basic health check for all used CO APIs.
* Added logging for all outgoing requests
* Various minor fixes and improvements

## v0.3.0

* config: data_service_name_person_data renamed to data_service_name_students
* config: added new required data_service_name_applications
* All commands now fall back to the person ID if the obfuscated isn't found
* Add new applications API
* Adjust other APIs for new attributes and adjust the mock tests to the new response format
* Command dbp:relay:cabinet-connector-campusonline:show-person-data renamed to
  dbp:relay:cabinet-connector-campusonline:show-student

## v0.2.0

* Port to the new CO APIs
* The config option `data_service_name_active_studies` was renamed to `data_service_name_studies`
* The command `show-active-studies` was renamed to `show-studies`
* In the JSON output the field `activeStudies` was renamed to `studies`

## v0.1.1

* Add various CLI commands for testing the API
* Added a bundle configuration for the API

## v0.1.0

* Initial release
