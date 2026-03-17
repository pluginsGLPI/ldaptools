# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-01-01

### Added
- GLPI 11.x compatibility
- PSR-4 autoloading: classes moved from `inc/` to `src/` under namespace `GlpiPlugin\Ldaptools`
- PHP 8.1 minimum requirement
- Bootstrap 5 / Tabler UI: replaced legacy `tab_cadre`, `tab_cadrehov`, `tab_bg_2` CSS classes
- Tabler Icons: replaced Font Awesome (`far fa-thumbs-up/down`, `fas fa-spinner`) with `ti ti-*` equivalents
- Empty state on test page when no LDAP directories are configured
- Error handler in AJAX test (network failures shown inline)
- Helper functions in `ajax/test.php` for consistent OK/fail/skip/warn spans

### Changed
- `setup.php`: uses `Glpi\Plugin\Hooks` constants; `Menu::class` reference instead of string
- `hook.php`: simplified — plugin creates no database tables
- `front/menu.php` and `front/test.php`: removed legacy `include('../../../inc/includes.php')`
- `ajax/test.php`: removed `$AJAX_INCLUDE = 1`; null-safe `$_GET` access
- `templates/menu.html.twig`: card-based layout, Bootstrap 5 alert for LDAP extension warning
- `templates/test.html.twig`: Bootstrap 5 table, spinner replaced with `ti-loader-2`, colspan corrected to 8

### Removed
- `inc/menu.class.php` (replaced by `src/Menu.php`)
- `inc/test.class.php` (replaced by `src/LdapTest.php`)
- Inline `style="color: green/red/orange"` attributes throughout

## [0.2.0] - 2022-xx-xx

- GLPI 10.0.x support

## [0.1.0] - 2021-xx-xx

- Initial release supporting GLPI 9.5.x and 10.0.x
