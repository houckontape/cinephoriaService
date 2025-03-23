<?php

namespace src\Models;

use Cassandra\Date;
use core\Orm\ORM;
use Exception;
use src\Models\Models;

class MovieEntity implements Models
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $title;
    /**
     * @var string
     */
    public string $synopsis;
    /**
     * @var string
     */
    public string $poster;
    /**
     * @var string
     */
    public string $trailer;
    /**
     * @var string
     */
    public string $releaseDate;
    /**
     * @var string
     */
    public string $note;
    /**
     * @var string
     */
    public string $weLike;
    /**
     * @var string
     */
    public string $duration;

     /**
     * @var Date
     */
    public date $createdAt;
    /**
     * @var Date
     */
    public date $updatedAt  ;

    public function __construct(int|null $id=null,array|null $arrayMovie=null)
    {
        $this->orm = new ORM();
        if($id != null){
            $this->id = $id;
            $result = self::recoveryById($id,$this->orm);
            $this->title = $result[0]['title'];
            $this->synopsis = $result[0]['synopsis'];
            $this->poster = $result[0]['poster'];
            $this->trailer = $result[0]['trailer'];
            $this->releaseDate = $result[0]['releaseDate'];
        }else{
            $this->title = $arrayMovie['title'];
            $this->synopsis = $arrayMovie['synopsis'];
            $this->poster = $arrayMovie['poster'];
            $this->trailer = $arrayMovie['trailer'];
            $this->releaseDate = $arrayMovie['releaseDate'];
        }

    }


    /**
     * @inheritDoc
     */
    public function save()
    {
        // Assuming ORM\Database is the library used
        if (empty($this->title) || empty($this->duration) || empty($this->poster)) {
            throw new Exception("Mandatory fields are missing.");
        }

        $this->id= $this->orm->insertAndGetId('movieentity',[
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'poster' => $this->poster,
            'trailer' => $this->trailer,
            'relaseDate' => $this->releaseDate,
            'note' => $this->note,
            'weLike' => $this->weLike,
            'duration' => $this->duration,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ]);

        return ['id'=>$this->id] ;
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
        $movie = $this->orm->select('movieentity', ['title'], ['id' => $id]);
        if($movie != null){
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
        return $orm->select('movieentity', ['*'], ['id' => $id]);
    }
}