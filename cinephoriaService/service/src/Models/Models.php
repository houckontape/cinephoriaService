<?php

namespace src\Models;

interface Models
{


    /**
     * @uses enregistre les données en bdd
     * @return mixed
     */
    public function save();

    /**
     * @uses supprime une ligne de la bdd
     * @return mixed
     */
    public function deleteById($id);

    /**
     * @uses met a jour la table
     * @return mixed
     */
    public function updateById($id);

    /**
     * @uses verifie si une ligne existe
     * @param $id
     * @return mixed
     */
    public function ifExist($id);

    /**
     * @uses instancie la classe avec les valeurs en bdd
     * @param $id
     * @return mixed
     */
    static function recoveryById($id,$orm);
}