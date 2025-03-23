<?php

namespace src\Models;

use core\Orm\ORM;
use src\Models\Models;

class GenreEntity implements Models
{
    public int $id;
    public string $name;
    public int $minimumAge;

    public ORM $orm;



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