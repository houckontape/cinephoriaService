# Documentation  version alpha 0.0.1
framework api 


## Les normes de codages 
php8.4 POO 

### Nomages des variables

Toutes les variables sont en anglais.  
Nous utiliserons la convention CamelCases.  

### architecture du projet

Le dossier public doit etre le dossier exposé dans la config apache2 
le point d entree de notre application sera le index.php
toutes les requetes seront rediriger vers le index.php a l aide d un fichier .htaccess

## Fonctionement

### le routage

depuis le point d entree index.php nous instencions la classe Router  
 la classe Route va chercher les routes possible ( classe contennnue dans src/Controller/Routes)
 on instencie la classe et on lance la methode de classe correspondant a la methode de requette http 
 nous faisons un reotur sous forme de tableau (par la suite le tableau sera traiter pour renvoyer un json) 

### base de donnée

la bdd sera gerer depuis un ORM .
les tables seront creer de manieres automatique depuis les classes dans le dossier api/src/Models.
la connection et les requetes seront traité depuis l Orm.

### systeme authentification


### les routes 
methode get
/users?id=XX   renvoie l utilisateur 
methode post 
envoie d un json 
```
 {
  "username":"XXXX",
  "userEmail":"XXXXX@noreply.com",
  "userPassword":"XXXXXXXXX"  
}
```
/users 
return un id .

/auth methode Post 
return 
```
{
"userId":1,
"userToken":"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
"userTokenExpiration":"1740077381",
"username":"xxxxxx",
"userEmail":"xxxxxx@noreply.com",
"userRole":1,
"userStatus":"en attente"
}
```