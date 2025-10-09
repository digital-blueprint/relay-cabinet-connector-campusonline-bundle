# CLI Commands

The bundle provides various CLI commands, mainly for testing and inspecting the fetched data.

## API Inspection Commands

These commands show the data received from the CO API in a table format without
post-processing. All commands take a CAMPUSonline obfuscated ID as input.

```console
$ ./bin/console dbp:relay:cabinet-connector-campusonline:show-student --help                                 
Description:
  Show student data for an obfuscated ID

Usage:
  dbp:relay:cabinet-connector-campusonline:show-student <obfuscated-id>

Arguments:
  obfuscated-id         obfuscated id
```


```console
$ ./bin/console dbp:relay:cabinet-connector-campusonline:show-studies --help
Description:
  Show studies for an obfuscated ID

Usage:
  dbp:relay:cabinet-connector-campusonline:show-studies <obfuscated-id>

Arguments:
  obfuscated-id         obfuscated id

```

```console
$ ./bin/console dbp:relay:cabinet-connector-campusonline:show-applications --help
Description:
  Show applications of a student

Usage:
  dbp:relay:cabinet-connector-campusonline:show-applications <obfuscated-id>

Arguments:
  obfuscated-id         obfuscated id
```

## Sync Commands

This gives the combined student data, converted to json, in the format that will be forwarded to cabinet.
See [Student Data](./data.md) for an example and more details on the format.

```console
$ ./bin/console dbp:relay:cabinet-connector-campusonline:sync-one --help                          
Description:
  Show JSON for an obfuscated ID

Usage:
  dbp:relay:cabinet-connector-campusonline:sync-one <obfuscated-id>

Arguments:
  obfuscated-id         obfuscated id

```

This command triggers a full sync of all students. The status of the sync will be cached which means repeated calls
will only fetch potentially changed entries unless `--full` is passed. This command doesn't output any data and is
mainly intended for standalone testing of the sync logic. 

```console
$ ./bin/console dbp:relay:cabinet-connector-campusonline:sync --help
Description:
  Run a sync

Usage:
  dbp:relay:cabinet-connector-campusonline:sync [options]

Options:
      --full            Force a full sync
```
