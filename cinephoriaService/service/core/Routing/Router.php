<?php
namespace core\Routing;
use core\Routing\Route;

class Router
{
    private array $routes = [];
    private string $method = '';
    private string $uri = '';
    private array $param = [];
    private string $controller= '';
    private string $action = '';
    private array $post = [];

    public function __construct(array $post=[])
    {
        $router = new Route();
        $this->routes=$router->getRoutes();
        //var_dump($this->routes);
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        if(isset($_SERVER['REDIRECT_URL'])){
            $this->uri = $_SERVER['REDIRECT_URL'];
        }else{
            echo "Pas de chemin";
        }

        parse_str($_SERVER['QUERY_STRING'],$this->param);
        $postController = explode('/', $this->uri )[1];
        if(isset($postController)){
            $this->controller = explode('/', $this->uri )[1];
        }else{
            exit('404');
        }

        if(!empty($post)){
            $this->post = $post;
        }
    }

    // on charge la routes pour pouvoir instancier la classe avec l autoload

    public function extractController(){
        if(isset($this->routes[$this->controller])){
            $object = new $this->routes[$this->controller]['controller']();
            return $object;
        }else{
           return false;
        }
    }

    public function extractAction():array
    {
        if(isset($this->routes[$this->controller])){

            $objectController = $this->extractController();
            //print_r($objectController);
            if(!empty($this->param)){
                 return $objectController->{$this->method}($this->param);
            }elseif(!empty($this->post)){
                //var_dump($this->post);
                return $objectController->{$this->method}($this->post);
            }else{
                return $objectController->{$this->method}();
            }

        }else{
            return ['Erorr 404'];
        }
    }






}