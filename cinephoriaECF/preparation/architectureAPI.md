# Arictecture APi


## Choix:

Nous choisirons une APi RESTful.
L'objectif est de créer une API robuste, sécurisée et évolutive qui permettra aux applications frontend (web et mobile)  
d'accéder aux données et aux fonctionnalités de l'application.

## Identification des ressources :
- films
- cinemas
- salles
- sceances
- incidents
- utilisateurs

## definitions des urls
methode GET:

/movie => liste des films

/movieTheater =>liste des cinema

/screening =>liste des salles

/session  =>liste des sceances

/incident => liste des incidents

/user =>liste des utilisateurs

/role =>liste des roles dispo

/XXXX/{id}  recuperation d'un item avec l id {id}

## Modelisation des données 

format JSON
voir les fichiers modeles de données.

## Sécurité de l'API

Authentification des utilisateurs avec un login mot de passe 
Recuperation d'un jeton d'accés pour interroger l'api avec limitation dans le temps.

## Documentation de l'API

nous utiliserons swagger pour documenter l api
