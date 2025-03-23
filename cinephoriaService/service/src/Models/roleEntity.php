<?php

namespace src\Models;

use core\Orm\ORM;
use src\Models\Models;

class roleEntity implements Models
{
    public string $roleName;
    public int $id;
    public ORM $orm;

    /**
     * @param ORM $orm
     */
    public function __construct(array $arrayRole)
    {
        $this->orm = new ORM();
        if(empty($arrayRole['role_name'])){
        $result=self::recoveryById($arrayRole['id'],$this->orm);
        $this->roleName = $result[0]['role_name'];
        }else{
        $this->roleName = $arrayRole['role_name'];
        }

        $this->id = $arrayRole['id'];

    }


    /**
     * @inheritDoc
     */
    public function save()
    {
        // TODO: Implement save() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        // TODO: Implement deleteById() method.
    }

    /**
     * @inheritDoc
     */
    public function updateById($id)
    {
        // TODO: Implement updateById() method.
    }

    /**
     * @inheritDoc
     */
    public function ifExist($id)
    {
        // TODO: Implement ifExist() method.
    }

    /**
     * @inheritDoc
     */
    static function recoveryById($id,$orm)
    {
        $result = $orm->orm->select('users', ['*'], ['id' => $id]);
        return $result;
    }
}