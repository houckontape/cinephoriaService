<?php

namespace src\Models;

use Cassandra\Date;
use src\Models\Models;

class SessionEntity implements Models
{
    public int $id;

    public date $date;

    public int $id_movie;


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
    static function recoveryById($id, $orm)
    {
        // TODO: Implement recoveryById() method.
    }
}