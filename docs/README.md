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
    # The data service name for the PersonData CO API
    data_service_name_person_data: ~ # Required, Example: loc_apiDmsStudPersMv
    # The data service name for the ActiveStudies CO API
    data_service_name_active_studies: ~ # Required, Example: loc_apiDmsStudien
```
