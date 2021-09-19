# vfx-tree-view

## About

Empty

## Requirements

Vfx-tree-view requires:

-   PHP version 8.0 or greater
-   [YAML](https://pecl.php.net/package/yaml) extension for PHP

<!---File `yaml.dll` is Windows 10 64 bit DLL library for LibYAML.
It located in `DLL` folder, put it in PHP binary or Windows/System directory.--->

## Config

All parameters stored in `config.yaml`

```yaml
---
title: Great VFX list
version: 2
timezone: Europe/Moscow
vault: /Path/to/Vault

vendors:
  - path: jane_works/in/#
    name: Jane

  - path: path2/in/hires/#
    name: John

  - path: acme/in/*/#*/EXR

  - path: path2/in/#
    name: Kit

regexp:
  - re: /^(?P<scene>[A-Z]{2,3})_(?P<index>\d{4})_?(?P<opt>\w+)?_V(?P<ver>\d{1,3})_?(?P<mod>\w+)?$/iu
    type: valid

  - re: /^(?P<scene>[A-Z]{3})_(?P<index>\d{4})(?:_(?P<rem>\w+))?$/iu
    type: warn
```

| parameter  | type   | description                                                                                                       |
| ---------- | ------ | ----------------------------------------------------------------------------------------------------------------- |
| _title_    | string | Main title                                                                                                        |
| _version_  | int    | **must be** `2`                                                                                                   |
| _timezone_ | TZ     | The timezone identifier, like UTC, Asia/Hong_Kong, or Europe/Moscow.                                              |
| _vault_    | string | Path to place where vendor's directories are stored.                                                              |
| _name_     | string | **(optional)** Vendor's name                                                                                      |
| _path_     | string | Path to vendor's directory relative vault's path. If a _name_ is missing first part of path used as vendor's name |
| _re_       | regex  | PRCE2 regular expression                                                                                          |
| _type_     | string | Type of shot matcher this regexp. It could be `valid`, `warn`.                                                    |

Patterns:

- `#`: date in _YYYYMMDD_ format
- `*`: any symbols _(using php glob() function)_

> All paths and names **must** be unique.

## License

Free software under MIT. Please share your modification to respect term of license.
