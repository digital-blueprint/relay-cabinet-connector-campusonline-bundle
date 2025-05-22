# Changelog

## Unreleased

## v0.3.20

* Update cabinet bundle

## v0.3.19

* Drop support for PHP 8.1
* Remove dependency on api-platform

## v0.3.18

* Add support for kevinrob/guzzle-cache-middleware v6

## v0.3.17

* Fix partial students syncs when exclude_inactive is enabled
* sync command: show if the sync was a full one or not
* config: add "api_time_zone" option to set the timezone for the CO server
* config: move exclude_inactive and cache into an "internal" section
* sync-one command: use caching if enabled
* sync-one command: integrate with the sync cursor, like the PHP API does
* partial sync: fall back to a full sync in case there are too many changes (>200 currently)
* Various documentation updates

## v0.3.16

* Fix book keeping of live application updates
* Change cursor serialization to use JSON instead of serialize/unserialize

## v0.3.15

* When 'exclude_inactive' is enabled make sure to filter out inactive studies
  and applications for inactive studies as well.

## v0.3.14

* Various field renames and type changes
  * Make all IDs strings instead of ints
  * Add identNumberObfuscated field
  * studentPersonNumber -> studentPersonId
  * studend.id is now the internal ID and not identNumberObfuscated
  * termStart -> studyLimitStartSemester
  * termEnd -> studyLimitEndSemester
  * Add a nullable alpha3Code field to all countries/nationalities

## v0.3.13

* Compatibility with newer cabinet bundle versions

## v0.3.12

* Expose new studyAddressRegion/homeAddressRegion fields
* sync: Minor performance improvements
* Minor logging improvements

## v0.3.11

* Handle null `IMMATRICULATIONSEMESTER`
* Add new `page_size` option to the bundle config

## v0.3.10

* Add parsing for `EXMATRICULATIONSEMESTER` for the student entity.
* Minor documentation improvements.
* Drop support for Symfony 5 and api-platform 2.x.

## v0.3.9

* When exclude_inactive is active, filter out inactive records in the delta and single sync too.

## v0.3.8

* Rename all `studentAddress*` fields to `studyAddress*` in the JSON output.
* `immatriculationDate` and `immatriculationSemester` study fields are now optional.

## v0.3.7

* Expose more fields via PersonSyncInterface
* Add support for translations fallbacks for enums, in case CO gains new values
* Include CO edit web URLs for persons and studies in the JSON output
* Include a sync date in the JSON output (when the data was fetched from the CO DB)
* Handle manual sync in combination with the delta sync. Delta sync will no longer
  return outdated records.

## v0.3.6

* Adjust for CO API changes
* Expose more fields via PersonSyncInterface

## v0.3.5

* Rename show-json command to sync-one
* In the sync command cache the cursor and add a "--full" option to start a full sync
* studies: include more fields in the JSON output
* studies: add support for parsing additionalCertificates

## v0.3.4

* Add "cache" config option to enable/disable caching. Useful for testing/development.

## v0.3.3

* Make exclude_inactive work with the PersonSyncInterface

## v0.3.2

* Implement new cabinet service interface

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
