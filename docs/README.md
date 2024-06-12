# Config

Created via `./bin/console config:dump-reference dbp_relay_cabinet_connector_campusonline | sed '/^$/d'`

```yaml
dbp_relay_cabinet_connector_campusonline:
  # The base URL of the CO instance
  api_url:              ~ # Required, Example: 'https://online.mycampus.org/campus_online'
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
```
