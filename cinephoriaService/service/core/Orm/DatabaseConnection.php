<?php

namespace core\Orm;
use core\Env\LoadEnv;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $connection = null;
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            LoadEnv::load('/home/tuxfarm/api/config/.env');
            LoadEnv::validate(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);

            try {
                // Configurer vos paramètres de connexion à la DB
                $dsn = "mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'].";charset=utf8mb4";
                $username = $_ENV['DB_USER'];
                $password = $_ENV['DB_PASSWORD'];

                // Créer une instance PDO
                self::$connection = new PDO($dsn, $username, $password);

                // Configurer les options de PDO
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Erreur lors de la connexion à la base de données : ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}