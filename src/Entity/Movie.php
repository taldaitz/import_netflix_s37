<?php

namespace Dawan\ImportNetflix\Entity;

use PDO;

class Movie
{
    public int $id = 0;
    public string $title;
    public string $country;
    public int $release_year;
    public string $description;
    public int $duration;

    public function __construct(array $csvLine)
    {
        $this->title = $csvLine[2];
        $this->country = substr($csvLine[5], 0, 100);
        $this->release_year = $csvLine[8];
        $this->description = $csvLine[12];
        $this->duration = (explode(' ', $csvLine[10]))[0];
    }

    public function save(PDO $connection) 
    {
         //Envoyer la requete de creation de film
        $sqlQuery = "INSERT INTO movie (title, country, release_year, description, duration) VALUES (:title, :country, :release_year, :description, :duration);";
        $statement = $connection->prepare($sqlQuery);
        $statement->bindParam(':title', $this->title, PDO::PARAM_STR);
        $statement->bindParam(':country', $this->country, PDO::PARAM_STR);
        $statement->bindParam(':release_year', $this->release_year, PDO::PARAM_INT);
        $statement->bindParam(':description', $this->description, PDO::PARAM_STR);
        $statement->bindParam(':duration', $this->duration, PDO::PARAM_INT);

        $statement->execute();

        $this->getIdFromDB($connection);
        
    }

    public function getIdFromDB(PDO $connection)
    {
        $checkQuery = "SELECT id FROM movie WHERE title = :title AND release_year = :release_year";
        $checkStatement = $connection->prepare($checkQuery);
        $checkStatement->bindParam(':title', $this->title, PDO::PARAM_STR);
        $checkStatement->bindParam(':release_year', $this->release_year, PDO::PARAM_INT);

        $checkStatement->execute();
        $result = $checkStatement->fetchAll();

        $this->id= $result[0]['id'];
    }
}