# About

# Requirements
Vfx-tree-view requires PHP version 8.0 or greater andYaml extension for PHP
Add [YAML](https://pecl.php.net/package/yaml) PHP extension.
**yaml.dll** is windows 64 bit DLL library for LibYAML.
It located in dll directory put it in PHP binary or Windows/System directory

# Config

All config stored in **config.yaml**

```
---
 title: Great VFX list
 vendors:
  -
    name: One
    path: One/in/#
  -
    name: Two
    path: Two/in/hires/#
  -
    path: Three/in/*/#/EXR
  -
    name:Four
    path: Four/in/#
 timezone: Europe/Moscow
 vault: /Path/to/Vault
 regexp:
    -
      re: /^(?P<scene>[A-Z]{2,3})_(?P<index>\d{4})_?(?P<opt>\w+)?_V(?P<ver>\d{1,3})_?(?P<mod>\w+)?$/iu
      type: valid
    -
      re: /^(?P<scene>[A-Z]{3})_(?P<index>\d{4})(?:_(?P<rem>\w+))?$/iu
      type: warn
```

\# -  date in YYYYMMDD format\
\* \- any symbols (using php glob() function)

