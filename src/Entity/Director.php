<?php

namespace Dawan\ImportNetflix\Entity;

use Exception;
use PDO;

class Director
{
    public int $id = 0;
    public string $firstname;
    public string $lastname;

    public function __construct(string $firstname, string $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public static function getDirectorsFromCsvLine(string $csvLine) : array
    {
        $directors = [];
        $directorNames = explode(',', $csvLine);

        foreach($directorNames as $dirName) {
            $names = explode(' ', trim($dirName));
            $directors[] = new Director($names[0], $names[1]);
        }

        return $directors;
    }

    public function save(PDO $connection) {

        $this->getIdFromDB($connection);

        if($this->id == 0) {
            $sqlDirectorQuery = "INSERT INTO director (firstname, lastname) VALUES (:firstname, :lastname);";
            $directorStatement = $connection->prepare($sqlDirectorQuery);
            $directorStatement->bindParam(':firstname', $this->firstname, PDO::PARAM_STR);
            $directorStatement->bindParam(':lastname', $this->lastname, PDO::PARAM_STR);

            $directorStatement->execute();
            $this->getIdFromDB($connection);
        }
    }

    public function getIdFromDB(PDO $connection) {
        $checkQuery = "SELECT id FROM director WHERE firstname = :firstname AND lastname = :lastname;";
        $checkStatement = $connection->prepare($checkQuery);
        $checkStatement->bindParam(':firstname', $this->firstname, PDO::PARAM_STR);
        $checkStatement->bindParam(':lastname', $this->lastname, PDO::PARAM_STR);

        $checkStatement->execute();
        $result = $checkStatement->fetchAll();

        $this->id = $result[0]['id'] ?? 0;
    }

    public function associateMovie(PDO $connection, Movie $movie) {
        try {
            $associateQuery = "INSERT INTO movie_director (director_id, movie_id) VALUES (:dir_id, :mov_id);";
            $associateStatement = $connection->prepare($associateQuery);
            $associateStatement->bindParam(':dir_id', $this->id, PDO::PARAM_INT);
            $associateStatement->bindParam(':mov_id', $movie->id, PDO::PARAM_INT);

            $associateStatement->execute();
        } catch(Exception $ex) {
            echo "BUG : sur le film $movie->title\n";
        }
    }
}