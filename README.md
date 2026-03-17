# LDAP Tools — GLPI Plugin

[![License](https://img.shields.io/badge/license-GPLv3-blue.svg)](LICENSE.md)

LDAP Tools offers diagnostic utilities for LDAP directories configured in GLPI.

## Features

### LDAP configuration test

Runs 7 sequential checks against every LDAP directory (and its replicates) declared in GLPI:

1. **TCP stream** — verifies the host/port is reachable from the GLPI server
2. **BaseDN** — ensures the BaseDN field is not empty
3. **LDAP URI** — attempts `ldap_connect` to validate the URI
4. **Bind auth** — performs an LDAP BIND (or reports if anonymous/disabled)
5. **Generic search** — runs `(cn=*)` and counts up to 50 entries
6. **Filtered search** — runs the configured LDAP filter and counts results
7. **Attributes** — lists all available attributes on the first matched entry

## Compatibility

| Plugin version | GLPI version |
|---------------|--------------|
| 1.x           | 11.0.x       |
| 0.2.x         | 10.0.x       |
| 0.1.x         | 9.5.x / 10.0.x |

## Requirements

- PHP ≥ 8.1
- `php-ldap` extension enabled

## Installation

1. Download the [latest release](https://github.com/pluginsGLPI/ldaptools/releases)
2. Extract to your GLPI `plugins/` directory as `ldaptools/`
3. Go to **Setup > Plugins** and activate *LDAP Tools*
4. Access via **Tools > LDAP Tools**

## Usage

Navigate to **Tools > LDAP Tools > LDAP Test**. Each configured LDAP directory is tested automatically on page load via async requests. Results are shown in a colour-coded table (green = OK, orange = warning, red = failure).

## License

GPLv3 — see [LICENSE.md](LICENSE.md)
