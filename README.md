# DbpRelayCabinetConnectorCampusonlineBundle

[GitHub](https://github.com/digital-blueprint/relay-cabinet-connector-campusonline-bundle) |
[Packagist](https://packagist.org/packages/dbp/relay-cabinet-connector-campusonline-bundle) |
[Changelog](https://github.com/digital-blueprint/relay-cabinet-connector-campusonline-bundle/blob/main/CHANGELOG.md)

[![Test](https://github.com/digital-blueprint/relay-cabinet-connector-campusonline-bundle/actions/workflows/test.yml/badge.svg)](https://github.com/digital-blueprint/relay-cabinet-connector-campusonline-bundle/actions/workflows/test.yml)

The `dbp/relay-cabinet-connector-campusonline-bundle` is a Symfony bundle that
provides a connector from the
[dbp/relay-cabinet-bundle](https://packagist.org/packages/dbp/relay-cabinet-bundle)
to a custom made CAMPUSonline API for fetching student data, like information
about the student, their studies, and applications.

```mermaid
graph TB
    A[dbp/relay-cabinet-bundle] --> B[dbp/relay-cabinet-connector-campusonline-bundle]
    B --> C[CAMPUSonline API]
    C --> E[Student Information]
    C --> F[Studies Information]
    C --> G[Applications Information]

    style B stroke-width:3px
```

See the [documentation](./docs/README.md) for more information.
