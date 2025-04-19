<?php

namespace src\Models;

use Date;
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
     * @var string
     */
    public string $createdAt;
    /**
     * @var string
     */
    public string $updatedAt  ;

    public int $genreId;

    private ORM $orm;

    /**
     * @var array|null
     * @dbType TABLE
     */
    public array|null $genre;

    public function __construct(int|null $id=null,array|null $arrayMovie=null)
    {
        $this->orm = new ORM();
        if($id != null){
            $this->id = $id;
            $result = self::recoveryById($id,$this->orm);
            $this->hydrateMovie($result[0]);

        }else{
            $this->hydrateMovie($arrayMovie);
        }

        // Récupération des informations du genre (si genreId est défini)
        if (!empty($this->genreId)) {
            $this->genre = $this->fetchGenreData($this->genreId);
        }

    }


    /**
     * Hydrate l'objet MovieEntity avec un tableau de données
     *
     * @param array $data
     */
    private function hydrateMovie(array $data)
    {
        $this->title = $data['title'];
        $this->synopsis = $data['synopsis'];
        $this->poster = $data['poster'];
        $this->trailer = $data['trailer'];
        $this->releaseDate = $data['releaseDate'];
        $this->note = $data['note'];
        $this->weLike = $data['weLike'];
        $this->duration = $data['duration'];
        $this->genreId = $data['genreId'];
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

        $this->id= $this->orm->insertAndGetId('movie',[
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'poster' => $this->poster,
            'trailer' => $this->trailer,
            'releaseDate' => $this->releaseDate,
            'note' => $this->note,
            'weLike' => $this->weLike,
            'duration' => $this->duration,
            'createdAt' => date('Y-m-d'),
            'updatedAt' => date('Y-m-d'),
            'genreId' => $this->genreId,
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
        $movie = $this->orm->select('movie', ['title'], ['id' => $id]);
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
        return $orm->select('movie', ['*'], ['id' => $id]);
    }

    /**
     * renvoie tout les films present en base en lecture seule
     * @return array
     */
    static function recoveryAll(): array
    {
        $orm = new ORM();
        $list = $orm->select('movie', ['*']);
        //var_dump($list);
        // on parcour la list pour generer un tableau d objet MovieEntity
        $movies = [];
        foreach ($list as $movie) {
            $o_movie = new MovieEntity(null, $movie);
            $movies[] = $o_movie;
        }
        return $movies;
    }

    /**
     * Récupere les 5 derniers film de la table trier par date
     *
     * @return array
     */
    public static function recoveryLastMovie(){
        $orm=new ORM();
        $result = $orm->query('SELECT * FROM movie ORDER BY createdAt DESC LIMIT 5');
        $movies = [];
        foreach ($result as $movieData) {
            $movie = new MovieEntity(null, $movieData);
            $movies[] = $movie;
        }
        return $movies;

    }

    /**
     * Récupère les informations du genre depuis la table 'genre'
     *
     * @param int $genreId
     * @return array|null
     */
    private function fetchGenreData(int $genreId): ?array
    {
        $genreData = $this->orm->select('genre', ['*'], ['id' => $genreId]);
        return $genreData[0] ?? null;
    }


    /**
     * Retourne les données initiales pour la table associée
     *
     * @return array
     */
    public static function getInitialData(): array
    {
        return [
            [
            'id'=>1,
            'title'=>'God Save the Tuche',
            'synopsis'=>'Les Tuche mènent à nouveau une vie paisible à Bouzolles . Mais lorsque le petit-fils de Jeff et Cathy est sélectionné pour un stage de football à Londres, c’est l’occasion rêvée pour toute la famille d’aller découvrir l’Angleterre et de rencontrer la famille royale. Entre chocs culturels et maladresses, les Tuche se retrouvent plongés au cœur de la royauté anglaise, qui n’est pas près d’oublier leur séjour ! Les Tuche mènent à nouveau une vie paisible à Bouzolles. Mais lorsque le petit-fils de Jeff et Cathy est sélectionné pour un stage de football à Londres, c’est l\’occasion rêvée pour toute la famille d’aller découvrir l’Angleterre et de rencontrer la famille royale. Entre chocs culturels et maladresses, les Tuche se retrouvent plongés au cœur de la royauté anglaise, qui n’est pas près d’oublier leur séjour \!',
            'poster'=>'https://fr.web.img2.acsta.net/c_310_420/img/e1/22/e12277d75be8466492265681d0327c11.jpg',
            'trailer'=>'',
            'releaseDate'=>'2021-01-01',
            'note'=>2,
            'weLike'=>false,
            'duration'=>95,
            'createdAt'=>date('Y-m-d'),
            'updatedAt'=>date('Y-m-d'),
            'genreId'=>1,
            ],
            [
                'id'=>2,
                'title'=>'Jouer avec le feu',
                'synopsis'=>'Pierre élève seul ses deux fils. Louis, le cadet, réussit ses études et avance facilement dans la vie. Fus, l’aîné, part à la dérive. Fasciné par la violence et les rapports de force, il se rapproche de groupes d’extrême-droite, à l’opposé des valeurs de son père. Pierre assiste impuissant à l’emprise de ces fréquentations sur son fils. Peu à peu, l’amour cède place à l’incompréhension…',
                'poster'=>'https://fr.web.img6.acsta.net/c_310_420/img/02/c2/02c28607beda37fdf319f91f48c08e71.jpg',
                'trailer'=>'',
                'releaseDate'=>'2021-01-01',
                'note'=>2,
                'weLike'=>false,
                'duration'=>118,
                'createdAt'=>date('Y-m-d'),
                'updatedAt'=>date('Y-m-d'),
                'genreId'=>2,
            ],
            [
                'id'=>3,
                'title'=>'Un parfait inconnu',
                'synopsis'=>"New York, 1961. Alors que la scène musicale est en pleine effervescence et que la société est en proie à des bouleversements culturels, un énigmatique jeune homme de 19 ans débarque du Minnesota avec sa guitare et son talent hors normes qui changeront à jamais le cours de la musique américaine. ",
                'poster'=>"https://fr.web.img6.acsta.net/c_310_420/img/7b/09/7b09c04859a7716e5fc78882f2f4b530.jpg",
                'trailer'=>"",
                'note'=>2,
                'weLike'=>false,
                'duration'=>140,
                'createdAt'=>date('Y-m-d'),
                'updatedAt'=>date('Y-m-d'),
                'genreId'=>3,
            ],
        ];
    }

}