<?php

namespace src\Models;

use Cassandra\Date;
use core\Orm\ORM;
use Exception;
use src\Models\Models;

class authEntity implements Models
{
    public int $id;
    public int $userId;
    public string $userToken;
    public string  $userTokenExpiration;
    public ORM $orm;

    /**
     * @param string $userToken
     * @param int $userId
     * @param Date $userTokenExpiration
     */
    public function __construct(string $userToken, int $userId, string $userTokenExpiration)
    {
        $this->userToken = $userToken;
        $this->userId = $userId;
        $this->userTokenExpiration = $userTokenExpiration;
        $this->orm = new ORM();

    }


    /**
     * @inheritDoc
     */
    public function save()
    {
        $this->orm->insert('authentity', ['userToken'=>$this->userToken,'userId'=>$this->userId,'userTokenExpiration'=>$this->userTokenExpiration]);
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        try{
            $this->orm->delete('authentity', ['userToken'=>$this->userToken,'userId'=>$this->userId,'userTokenExpiration'=>$this->userTokenExpiration]);
            return true;
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function updateById($id)
    {
        $this->orm->update('authentity', ['userToken'=>$this->userToken,'userTokenExpiration'=>$this->userTokenExpiration], ['userId'=>$this->userId]);
    }

    /**
     * @inheritDoc
     */
    public function ifExist($id)
    {
       $auth=$this->orm->select('authentity', ['*'],['userId'=>$id]);
      //var_dump($auth);
       if(isset($auth[0]['userToken'])){
           return true;
       }else{
           return false;
       }
    }

    /**
     * @inheritDoc
     */
    static function recoveryById($id, $orm)
    {
        // TODO: Implement recoveryById() method.
    }
}