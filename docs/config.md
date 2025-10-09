
# Bundle Configuration

Created via `./bin/console config:dump-reference dbp_relay_cabinet_connector_campusonline | sed '/^$/d'`

```yaml
# Default configuration for extension with alias: "dbp_relay_cabinet_connector_campusonline"
dbp_relay_cabinet_connector_campusonline:
    # The base URL of the CO instance
    api_url:              ~ # Required, Example: 'https://online.mycampus.org/campus_online'
    # The time zone of the CO instance, used for timestamp conversion
    api_time_zone:        Europe/Vienna # Example: Europe/Vienna
    # The OAuth2 client ID
    client_id:            ~ # Required, Example: my-client
    # The OAuth2 client secret
    client_secret:        ~ # Required, Example: my-secret
    # The data service name for the Student CO API
    data_service_name_students: ~ # Required, Example: loc_api-dms.dmsstudents
    # The data service name for the Studies CO API
    data_service_name_studies: ~ # Required, Example: loc_api-dms.dmsstudies
    # The data service name for the Applications CO API
    data_service_name_applications: ~ # Required, Example: loc_api-dms.dmsapplicants
    # The page size used for CO requests
    page_size:            20000
    internal:
        # Set to exclude inactive students and studies (useful for testing with less data)
        exclude_inactive:     false # Example: 'true'
        # Enable caching for easier development
        cache:                false # Example: 'true'
        # threshold for when to force a full sync
        incremental_sync_threshold: 200
```

* `page_size` needs to be adjusted depending on the server/database performance
  and for the CO reverse proxy timeout configured. The time it takes to fetch a
  page also scales with the number of total entries, so in case the server is
  slow there might not be a page size that works reliably.

* `api_time_zone` is used to convert local time returned by the CO API to a
  timezone aware date time. If the time zone is not correct and does not match
  the CO time zone then timestamps returned to cabinet will be wrong, the sync
  itself will still work though.

* `internal.exclude_inactive` and `internal.cache` are only useful for testing
  and development and should not be used in production.

## Health Checks

To make sure the bundle configuration is correct you can run the bundle health checks via:

```bash
$ ./bin/console dbp:relay:core:check-health --only=cabinet-connector-campusonline
[cabinet-connector-campusonline]
  Check if the students API works: [SUCCESS]
  Check if the studies API works: [SUCCESS]
  Check if the applications API works: [SUCCESS]
```

It will try to connect to all three CO APIs
