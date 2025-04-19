<?php

namespace src\Controller\Routes;

use src\Controller\Routes\Route;
use src\Models\authEntity;
use src\Models\userEntity;

class Auth implements Route
{

    public function __construct()
    {
    }

    public function action()
    {
        // TODO: Implement action() method.
    }

    public function get($param = [])
    {
        if (isset($param['userEmail']) and isset($param['password'])) {
            userEntity::authUser($param['userEmail'],$param['password']);

        }
    }

    public function post(array $param=[])
    {
        // on teste si nous avons $param['userEmail'] et $param['userPassword]
        if(isset($param['userEmail']) and isset($param['userPassword'])){
           // on verifie que l email et le passwrod
            $auth=userEntity::authUser($param['userEmail'],$param['userPassword']);
            if(isset($auth->userId)){
                //on recupere le user authentifié
                $user=new userEntity($auth->userId);
                return [
                    'userId'=>$auth->userId,
                    'userToken'=>$auth->userToken,
                    'userTokenExpiration'=>$auth->userTokenExpiration,
                    'username'=>$user->username,
                    'userEmail'=>$user->userEmail,
                    'userIdRole'=>$user->userIdRole,
                    'userStatus'=>$user->userStatus
                ];
            }else{
                return ['error'=>'email or password incorrect'];
            }

        }else{
            return ['error'=>'no email or password'];
        }
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