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

    /**
     * Retourne les données initiales pour la table associée
     *
     * @return array
     */
    public static function getInitialData(): array
    {
        return [
            ['id'=>1,'name' => 'Comedie', 'minimumAge' => 0],
            ['id'=>2,'name' => 'Drame', 'minimumAge' => 12],
            ['id'=>3,'name' => 'Biopic', 'minimumAge' => 0],
            ['id'=>4,'name' => 'animation', 'minimumAge' => 0],
            ['id'=>5,'name' => 'Horreur', 'minimumAge' => 18],
        ];
    }

}