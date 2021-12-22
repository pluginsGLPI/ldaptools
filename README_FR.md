# Outils LDAP pour GLPI

[English README](README.md)

Ce plugin offre plusieurs outils en lien avec les annuaires LDAP déclarés dans GLPI.

## Premier outil : LDAP test des configurations

Effectue différents tests sur tous les annuaires LDAP déclarés dans GLPI :

1. test si le flux TCP est ouvert entre GLPI et le hostname/port du serveur LDAP
2. vérifie que le champ "BaseDN" est correctement rempli
3. lance un "ldap_connect" pour valider l'URI LDAP
4. exécute ou non une authentification LDAP BIND (avec utilisateur/mot de passe, ou certificat/clé TLS)
5. effectue une recherche LDAP générique (avec cn=\*) et essaye de compter les premières entrées
6. effectue une recherche LDAP spécifique (avec le filtre LDAP configuré) et essaye de compter les premières entrées 
7. récupère et affiche les attributs LDAP disponible sur la première entrée trouvée

## Screenshots

![LDAP Test all result](docs/screenshots/test.all.fullresult.png)
