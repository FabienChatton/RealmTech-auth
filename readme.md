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
### code
La méthode d'implémentation "code" propose d'utilisé d'utilisé un secret commun entre
le client et le serveur d'authentification, le mot de passe de l'utilisateur.
Grace à une fonction de hachage, le mot de passe peut être transmit de manière sûr
du client au serveur. Le message est le nom d'utilisateur pour 
permettre de récupérer le mot de passe sur le serveur.
Cette méthode est inspiré des Code d'authentification de message.
[Wikipedia Code d'authentification de message](https://fr.wikipedia.org/wiki/Code_d%27authentification_de_message)

```mermaid
sequenceDiagram
    participant RealmTech-client
    participant RealmTech-server
    participant RealmTech-auth
    participant Database

    RealmTech-client -)+ RealmTech-auth: hashPassword
    RealmTech-auth --)- RealmTech-client: hashedPassword
    RealmTech-client ->> RealmTech-client: generateCode
    RealmTech-client -)+ RealmTech-server: sendCodeAndMessage
    RealmTech-server -)+ RealmTech-auth: verifyCodeAndMessage
    RealmTech-auth ->> Database: getPassword
    Database -->> RealmTech-auth: password
    RealmTech-auth ->> RealmTech-auth: generateCode
    RealmTech-auth ->> RealmTech-auth: compareCode
    RealmTech-auth --)- RealmTech-server: accessGranted
    RealmTech-server --)- RealmTech-client: accessGranted
```

un petit complément pour générer et vérifié le code.
```mermaid
flowchart
    subgraph client
        MessageClient[nom d'utilisateur]
        HashClient[fonction de hachage]
        CodeClient[CodeClient]
        PasswordClient[mot de passe client hachée]
    end
    subgraph reseau
        MessageReseau[nom d'utilisateur]
        CodeReseau[CodeClient]
    end
    subgraph serveur
        MessageServer[nom d'utilisateur]
        HashServer[fonction de hachage]
        CodeServer[CodeClient]
        CodeServerClient[CodeServer]
        PasswordServer[mot de passe client hachée]
        CompareHash[compare code serveur code client]
        Database
    end

    MessageClient --> MessageReseau
    MessageClient --> HashClient
    PasswordClient --> HashClient
    HashClient --> CodeClient
    CodeClient --> CodeReseau

    CodeReseau --> CodeServer
    MessageReseau --> MessageServer

    PasswordServer --> HashServer
    MessageServer --> HashServer
    HashServer --> CodeServerClient
    MessageServer --> Database
    Database --> PasswordServer
    
    CodeServer --> CompareHash
    CodeServerClient --> CompareHash

```
