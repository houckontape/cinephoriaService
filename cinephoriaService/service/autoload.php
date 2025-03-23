<?php
/**
 * Autoloader pour charger automatiquement les classes.
 */

// Enregistrer une fonction de chargement automatique des classes
spl_autoload_register(function ($className) {
    // Convertir le namespace de la classe en chemin
    $classPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $className) . '.php';
    //echo $classPath."<br>";
    // Parcourir les répertoires pour trouver la classe
    $filePath = __DIR__. DIRECTORY_SEPARATOR . $classPath;
    //echo $filePath."<br>";
    // Si le fichier est trouvé, on le charge
    if (file_exists($filePath)) {
        require_once $filePath;
        //echo "fichier chargé"."<br>";
        return; // On arrête si le fichier a été trouvé
    }


    // En cas d'absence, lancer une erreur
    throw new Exception("Impossible de charger la classe : $className");
});
