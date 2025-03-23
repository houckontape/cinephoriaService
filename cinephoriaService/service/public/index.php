<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// on recupere l autoload
ini_set('display_errors', 1);
require_once '../autoload.php';
// on charge les classes
use core\Routing\Router;
//print_r($_SERVER);

//var_dump($_POST);
//var_dump($_COOKIE);
//var_dump($_GET);
//var_dump($_SERVER);
//var_dump($_FILES);
//var_dump($_ENV);
//var_dump($_REQUEST);
//var_dump($_SESSION);

if($_SERVER['REQUEST_METHOD']=='POST'){
    $json_data=file_get_contents('php://input');
    $data = json_decode($json_data, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $router=new Router($data);
        //var_dump($router);
        echo  json_encode($router->extractAction());
    }else{
        $retour['error']='json error';
        $retour['error_code']=json_last_error();
        $retour['error_msg']=json_last_error_msg();
        echo json_encode($retour);
    }
}else{
    $router=new Router();
    echo  json_encode($router->extractAction());
}






// Supprime le chemin de base si nécessaire (exemple: /api/public/)
