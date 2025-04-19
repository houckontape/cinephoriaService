<?php

namespace src\Controller\Routes;

use src\Controller\Routes\Route;
use src\Models\MovieEntity;

class Movies implements Route
{
    private MovieEntity $movie;
    public function __construct()
    {

    }

    public function action()
    {
        // TODO: Implement action() method.
    }

    public function get($param=[])
    {
        if(isset($param['id'])){
           return [$this->movie = new MovieEntity($param['id'])];
        }elseif (isset($param['last'])){
           return MovieEntity::recoveryLastMovie();
        }else{
            return MovieEntity::recoveryAll();
        }
    }

    public function post($data): array
    {
        //var_dump($data);
        // nous allons implementer la creation d'un film
        $this->movie=new MovieEntity(0,$data);
        $this->movie->save();
        return ['success'=>'movie created'];
    }

    public function put()
    {
        // TODO: Implement put() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function patch()
    {
        // TODO: Implement patch() method.
    }

    public function options()
    {
        // TODO: Implement options() method.
    }
}