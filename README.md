# LDAP Tools for GLPI

[README Français](README_FR.md)

This plugin offers several tools related to LDAP directories declared in GLPI.

## First tool : LDAP configurations tests

Performs various tests on all LDAP directories (and replicates) declared in GLPI :

1. test if TCP stream is opened from GLPI to LDAP server hostname / port
2. check is "BaseDN" field is filled in correctly
3. initiate an "ldap_connect" to validate the LDAP URI
4. execute or not an LDAP BIND authentication (with user/password, or TLS cert/key)
5. perform a generic LDAP Search (with cn=\*) and try to count first entries
6. perform a specific LDAP Search (with LDAP Filter configured) and try to count first entries
7. get and display all LDAP attributes available on the first entry found

## Screenshots

![LDAP Test all result](docs/screenshots/test.all.fullresult.png)
