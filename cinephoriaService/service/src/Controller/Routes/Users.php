<?php

namespace src\Controller\Routes;

use Cassandra\Date;
use src\Models\userEntity;

class Users
{
    private userEntity $userEntity;


    public function __construct($id=0)
    {
        //echo "hello , i'm users ";

    }

    public function get($param=[])
    {
        if(isset($param['id'])){
           $this->userEntity=new userEntity(intval($param['id']));
            //var_dump($this->userEntity);
            $return=[
                'username'=> $this->userEntity->username,
                'userEmail'=> $this->userEntity->userEmail,
                'userPassword'=> '***********',
                'userRole'=> $this->userEntity->userIdRole,
            ];
        }else{
            $return=['error'=>'no id'];
        }


        return $return;
    }

    public function post(array $param=[])
    {
        //$arrayParam=json_decode($param,true);
        //var_dump($param);
        $this->userEntity=new userEntity(0,$param);
        $this->userEntity->save();
        return ['success'=>'user created'];
    }

}