<?php
namespace core\Routing;

use ReflectionClass;
use ReflectionMethod;

class Route
{
    public array $routes = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    /**
     * Récupère automatiquement toutes les routes à partir du dossier /src/Controller/Routes
     * Chaque classe et ses méthodes publiques sont considérées comme des routes.
     */
    private function loadRoutes()
    {
        $path = __DIR__ . '/../../src/Controller/Routes'; // Chemin vers le dossier
        $namespace = 'src\Controller\Routes'; // Namespace des contrôleurs

        // Vérifie si le chemin est valide
        if (!is_dir($path)) {
            throw new \Exception("Le dossier des routes n'existe pas : $path");
        }

        // Parcourir tous les fichiers .php du dossier donné.
        foreach (glob($path . '/*.php') as $file) {
            $className = $this->getClassNameFromPath($file, $namespace);
//            print_r($className);
//            echo "<br>";
            if (class_exists($className)) {
                //echo "la classe existe <br>";
                $this->registerRoutesFromClass($className);
            }
        }
    }

    /**
     * Génère le nom complet de la classe avec son namespace à partir d'un fichier.
     */
    private function getClassNameFromPath(string $filePath, string $namespace): string
    {
        // Extraire le nom du fichier
        $fileName = basename($filePath, '.php'); // Enlève l'extension .php
        return $namespace . '\\' . $fileName; // Génère le namespace complet
    }

    /**
     * Récupère toutes les routes d'une classe et les enregistre.
     */
    private function registerRoutesFromClass(string $className)
    {
        try {
            $reflection = new ReflectionClass($className);
        } catch (\ReflectionException $e) {
            echo $e->getMessage();
        }

        // Parcourt toutes les méthodes publiques de la classe
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            //echo "$method->class <br>";
            if ($method->class === $className) {
                // Utilisation conventionnelle : `nom_de_classe/nom_de_methode`
                $route = strtolower($reflection->getShortName());

                $this->routes[$route] = [
                    'controller' => $className,
                    'method' => $method->getName()
                ];
            }
        }
    }

    /**
     * Obtenez toutes les routes générées.
     */
    public function getRoutes()
    {
        //print_r($this->routes);
        return $this->routes;
    }
}