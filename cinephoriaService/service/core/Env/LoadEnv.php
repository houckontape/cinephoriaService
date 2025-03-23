<?php

namespace core\Env;
use Exception;

/**
 * @uses permet de charger les varaibles d environement
 * @uses // Charger les variables d'environnement depuis le fichier .env
 * LoradEnv::load(__DIR__ . '/.env');
 *
 * // Valider les clés obligatoires
 * LoradEnv::validate(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);
 */
class LoadEnv
{
    /**
     * Charge les variables d'environnement depuis un fichier .env.
     *
     * @param string $filePath Chemin absolu ou relatif vers le fichier .env.
     * @throws Exception Si le fichier est introuvable.
     */
    public static function load(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new Exception("Le fichier .env est introuvable : $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignorer les lignes de commentaire
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Séparer clé et valeur
            [$key, $value] = array_pad(explode('=', $line, 2), 2, null);

            if ($key && $value) {
                // Supprimer les espaces
                $key = trim($key);
                $value = trim($value);

                // Enregistrer les variables dans $_ENV et $_SERVER
                $_ENV[$key] = $value;
                //$_SERVER[$key] = $value;
            }
        }
    }

    /**
     * Récupère une variable d'environnement stockée.
     *
     * @param string $key Nom de la clé de la variable d'environnement.
     * @param mixed $default Valeur par défaut si la clé est introuvable.
     * @return mixed La valeur de la variable ou la valeur par défaut.
     */
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Vérifie que les variables d'environnement obligatoires existent.
     *
     * @param array $requiredKeys Liste des clés à vérifier.
     * @throws Exception Si une clé obligatoire est manquante.
     */
    public static function validate(array $requiredKeys): void
    {
        foreach ($requiredKeys as $key) {
            if (!isset($_ENV[$key])) {
                throw new Exception("La variable d'environnement '$key' est manquante dans le fichier .env.");
            }
        }
    }

}