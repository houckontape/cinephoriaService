# Utilisation de l orm


## creation des tables 

les tables sont creer par le script create_tables.php  
lancé au deployement avec la commandes 
```
php /var/www/api/core/Orm/createTable.php
```
les tables sont creer depuis les classes contennue dans src/Models

les type pris en charge sont 
varchar 255
int
double
json 
text

exemple de classe :

```
class User
{
public string $name;                  // VARCHAR(255) par défaut
public string $email;                 // VARCHAR(255) par défaut
public ?string $password;             // NULLABLE VARCHAR(255)
public int $age;                      // INT
public ?float $balance;               // NULLABLE DOUBLE

    /** @dbType TEXT */
    public string $bio;                   // TEXT (annotation détectée)

    /** @dbType JSON */
    public array $settings;               // JSON (tableau stocké sous format JSON)
}
```

1. Les propriétés `@dbType JSON` et `@dbType TEXT` sont détectées via les annotations PHPDoc.
2. Les propriétés de type `array` sont automatiquement mappées au type SQL `JSON`.
3. Les propriétés dépourvues de type PHP ou annotation sont mappées par défaut à `TEXT`.

