# RealmTech-auth

## introduction
RealmTech-auth est la partie d'authentification de RealmTech.
C'est ici que les utilisateur peuvent s'inscrire afin
d'avoir une persistance de leur compte joueur.

RealmTech-auth est aussi responsable pour garantir que c'est
le bon joueur qui se connect à un serveur.

## problématique
Le serveur de jeu (RealmTech-server) doit authentifie le client (RealmTech-client)
qui souhait ce connecter à un serveur de jeu grace à son identifiant stocké sur
le serveur central d'identifiant (RealmTech-auth). Les connexions avec le serveur
authentification sont considérées comme sûr. C'est là que le défi commence, car
le client doit donner des informations secrets au serveur sur une connexion
non sécurisée.

## implémentation de sécurité
### access token
La méthode d'implémentation "access token" exploit que seul le client peut modifier
la base de données. Le client va demander au serveur d'autentification de
créer un jeton d'access temporaire (access token), puis le serveur de
jeu va vérifié sur le jeton est valide.

#### diagramme pour vérification de l’authenticité.
```mermaid
sequenceDiagram
    participant RealmTech-client
    participant RealmTech-server
    participant RealmTech-auth
    participant Database

    RealmTech-client -)+ RealmTech-auth:  createToken(username, password)
    RealmTech-auth ->> Database: getPasswordHash(username)
    Database -->> RealmTech-auth: passwordHash
    RealmTech-auth ->> RealmTech-auth: verifyPassword(password, passwordHash)
    RealmTech-auth ->> RealmTech-auth: generateAccessToken()
    RealmTech-auth ->> Database: saveAccessToken
    RealmTech-auth --)- RealmTech-client: 200, ok
    RealmTech-client -)+ RealmTech-server: demandeDeConnexion(username)
    RealmTech-server -)+ RealmTech-auth: verifyToken(username)
    RealmTech-auth ->> Database: getUserAcessToken(username)
    Database -->> RealmTech-auth: accessToken
    RealmTech-auth ->> RealmTech-auth: verifyToken
    RealmTech-auth ->> Database: invalidateAccessToken()
    RealmTech-auth --)- RealmTech-server: accessGranted
    RealmTech-server --)- RealmTech-client: accessGranted
```
## Création d'un compte
La création d'un nouveau compte se fait sur un site internet (RealmTech-online), qui
communique avec RealmTech-auth afin de créer le compte.

### Diagramme de la création d'un compte
```mermaid
sequenceDiagram
    participant RealmTech-online
    participant RealmTech-auth
    participant Database

    RealmTech-online -)+ RealmTech-auth: registerNewUser(username, password)
    RealmTech-auth ->> RealmTech-auth: usernameHasRequirement(username)
    RealmTech-auth ->> RealmTech-auth: hashPassword(password)
    RealmTech-auth ->> Database: insertNewUser(username, passwordHash)
    Database ->> Database: generateRandomUuid
    Database ->> Database: getTimestamp
    Database ->> Database: newUser
    RealmTech-auth --)+ RealmTech-online: 201, resource
```
