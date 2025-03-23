<?php
/**
 * utilisation php createTable.php
 * 1. Si une table n'existe pas, elle est créée comme avant.
 * 2. Si une table existe :
 *  - Les modifications nécessaires (ajout, modification de colonnes) seront détectées et appliquées via des instructions `ALTER TABLE`.
 *  - Les colonnes obsolètes pourront être gérées si vous activez la suppression dans la fonction `getAlterTableSQL`.
 *
 */
require __DIR__ . '/autoload.php';
ini_set('display_errors', 1);
use core\Orm\DatabaseConnection;


// Définitions
const MODELS_NAMESPACE = 'src\Models';
const MODELS_DIRECTORY = __DIR__ . '/src/Models';
/**
 * Récupérer les colonnes de la table existante.
 *
 * @param PDO    $connection
 * @param string $tableName
 * @return array Liste des colonnes (nom => type SQL)
 */
function getTableColumns(PDO $connection, string $tableName): array
{
    $query = "DESCRIBE `$tableName`";
    $stmt = $connection->query($query);
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[$row['Field']] = $row['Type'];
    }

    return $columns;
}
/**
 * Générer les instructions ALTER TABLE si la structure de la classe est différente.
 *
 * @param string $tableName
 * @param array $existingColumns Les colonnes existantes dans la table
 * @param array $classProperties Les colonnes définies dans la classe
 * @return array Liste des instructions ALTER TABLE
 */
function getAlterTableSQL(string $tableName, array $existingColumns, array $classProperties): array
{
    $alterSQL = [];

    // Ajouter les colonnes manquantes
    foreach ($classProperties as $propertyName => $definition) {
        if (!array_key_exists($propertyName, $existingColumns)) {
            $alterSQL[] = "ADD COLUMN `$propertyName` {$definition['type']} {$definition['nullable']}";
        }
    }

    // Vérifier les colonnes existantes pour détecter les modifications ou suppressions
    foreach ($existingColumns as $columnName => $columnType) {
        if (!array_key_exists($columnName, $classProperties)) {
            // Si la colonne n'est plus présente dans la classe – optionnel : vous pouvez ignorer cela si vous ne voulez pas supprimer des colonnes
            //$alterSQL[] = "DROP COLUMN `$columnName`";
        } elseif ($classProperties[$columnName]['type'] !== $columnType) {
            // Si le type a changé
            $alterSQL[] = "MODIFY COLUMN `$columnName` {$classProperties[$columnName]['type']} {$classProperties[$columnName]['nullable']}";
        }
    }

    return $alterSQL;
}
/**
 * Appliquer les modifications à une table existante si nécessaire.
 *
 * @param PDO    $connection
 * @param string $tableName
 * @param array  $alterSQL
 */
function applyAlterTable(PDO $connection, string $tableName, array $alterSQL): void
{
    if (!empty($alterSQL)) {
        foreach ($alterSQL as $alter) {
            try {
                $connection->exec("ALTER TABLE `$tableName` $alter");
                echo "Modification appliquée : ALTER TABLE `$tableName` $alter\n";
            } catch (PDOException $e) {
                echo "Erreur lors de l'altération de la table `$tableName`: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "Aucune modification nécessaire pour la table `$tableName`.\n";
    }
}
/**
 * Lister toutes les classes présentes dans le dossier des modèles.
 *
 * @return array
 */
function getModelClasses(): array
{
    $classes = [];

    // Scanner les fichiers dans le répertoire des modèles
    foreach (scandir(MODELS_DIRECTORY) as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $className = MODELS_NAMESPACE . '\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                // Ignorer les interfaces et les classes abstraites
                if (!$reflection->isInterface() && !$reflection->isAbstract()) {
                    $classes[] = $className;
                }
            }
        }
    }

    return $classes;
}

/**
 * Vérifier si une table existe dans la base de données.
 *
 * @param PDO    $connection
 * @param string $tableName
 * @return bool
 */
function tableExists(PDO $connection, string $tableName): bool
{
    $query = "SHOW TABLES LIKE :tableName";
    $stmt = $connection->prepare($query);
    $stmt->execute([':tableName' => $tableName]);
    return $stmt->fetch() !== false;
}

/**
 * Mapper les types PHP vers les types SQL.
 *
 * @param string|null $phpType
 * @param string|null $annotation
 * @return string
 */
function mapPhpTypeToSqlType(?string $phpType, ?string $annotation = null): string
{
    // Si une annotation est fournie pour le type JSON ou TEXT
    if ($annotation === 'text') {
        return 'TEXT';
    }

    if ($annotation === 'json') {
        return 'JSON';
    }

    // Si le mapping repose exclusivement sur le type PHP
    return match ($phpType) {
        'string' => 'VARCHAR(255)',
        'int' => 'INT',
        'float', 'double' => 'DOUBLE',
        'bool' => 'TINYINT(1)', // Les booléens sont souvent stockés comme TINYINT(1)
        'array' => 'JSON',// Les tableaux en PHP deviennent JSON en SQL
        'Date' => 'DATETIME',
        default => 'TEXT',      // Par défaut, utiliser TEXT
    };
}

/**
 * Analyser les annotations dans les commentaires des propriétés (si disponibles).
 *
 * @param ReflectionProperty $property
 * @return string|null
 */
function getAnnotationForColumnType(ReflectionProperty $property): ?string
{
    $docComment = $property->getDocComment();
    if ($docComment) {
        if (str_contains($docComment, '@dbType TEXT')) {
            return 'text';
        }
        if (str_contains($docComment, '@dbType JSON')) {
            return 'json';
        }
        if (str_contains($docComment, '@dbType DATETIME')) {
            return 'datetime';
        }
    }
    return null;
}

/**
 * Créer ou modifier une table dans la base de données à partir d'une classe modèle.
 *
 * @param PDO    $connection
 * @param string $className
 */
function createTableFromClass(PDO $connection, string $className): void
{
    $reflection = new ReflectionClass($className);
    $tableName = strtolower($reflection->getShortName());
    $tableName = str_replace('models', '', $tableName);

    // Récupérer les propriétés de la classe
    $properties = $reflection->getProperties();
    if (empty($properties)) {
        echo "La classe `$className` n'a pas de propriétés. Aucune table ne sera créée/modifiée.\n";
        return;
    }

    // Créer la définition des colonnes à partir des propriétés de la classe
    $classProperties = [];
    foreach ($properties as $property) {
        $propertyName = $property->getName();
        if ($propertyName === 'orm' or $propertyName === 'id') {
            continue;
        }

        // Détecter le type PHP
        $type = $property->getType();
        $typeName = $type instanceof ReflectionNamedType ? $type->getName() : null;

        // Vérifier les annotations pour des types "TEXT" ou "JSON"
        $annotation = getAnnotationForColumnType($property);

        // Mapper les types PHP et annotations vers SQL
        $sqlType = mapPhpTypeToSqlType($typeName, $annotation);

        // Vérifier si la propriété est nullable
        $nullable = $type instanceof ReflectionNamedType && $type->allowsNull() ? 'NULL' : 'NOT NULL';

        $classProperties[$propertyName] = [
            'type' => $sqlType,
            'nullable' => $nullable,
        ];
    }

    // Vérifier si la table existe déjà
    if (tableExists($connection, $tableName)) {
        // Récupérer la structure actuelle de la table
        $existingColumns = getTableColumns($connection, $tableName);

        // Générer les modifications nécessaires
        $alterSQL = getAlterTableSQL($tableName, $existingColumns, $classProperties);

        // Appliquer les modifications
        applyAlterTable($connection, $tableName, $alterSQL);
    } else {
        // Construire la création de table
        $columnsDDL = [];
        foreach ($classProperties as $propertyName => $definition) {
            $columnsDDL[] = "`$propertyName` {$definition['type']} {$definition['nullable']}";
        }
        $columnsDDL = implode(",\n", $columnsDDL);

        $createTableSQL = "CREATE TABLE `$tableName` (\n`id` INT AUTO_INCREMENT PRIMARY KEY,\n$columnsDDL\n)";

        // Exécuter la création de la table
        try {
            $connection->exec($createTableSQL);
            echo "La table `$tableName` a été créée avec succès.\n";
        } catch (PDOException $e) {
            echo "Erreur lors de la création de la table `$tableName`: " . $e->getMessage() . "\n";
        }
    }
}
// Initialiser la connexion
$connection = DatabaseConnection::getConnection();
echo "connection ok \r\n";
// Lister toutes les classes dans src/Models
$modelClasses = getModelClasses();
//echo "Lister toutes les classes dans src/Models ok \r\n";
//var_dump($modelClasses);
// Parcourir chaque classe et créer une table si nécessaire
foreach ($modelClasses as $className) {
    createTableFromClass($connection, $className);
}
