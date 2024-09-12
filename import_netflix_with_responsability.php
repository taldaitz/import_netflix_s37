<?php

//Ouvre la connexion à mon CSV
$handle = fopen('netflix.csv', 'r');

//Ouvre une connexion à ma BDD
$db = new PDO('mysql:host=localhost;dbname=netflix', 'root', 'root');

//Supprimer le contenu de mes tables
$db->query('DELETE FROM movie_director;')->execute();
$db->query('DELETE FROM director;')->execute();
$db->query('DELETE FROM movie;')->execute();


//Lire les flims du fichier CSV ligne par ligne
while(($line = fgetcsv($handle, null, ";")) !== false) {
    if($line[1] != 'Movie') continue;

    //extraire les informations d'un film
    $title = $line[2];
    $country = substr($line[5], 0, 100);
    $release_year = $line[8];
    $description = $line[12];
    $duration = (explode(' ', $line[10]))[0];

    //extraire les informations d'un réalisateur
    $directors = explode(',', $line[3]);

    //Créer les réalisateurs en BDD
    $directorIds = [];
    foreach($directors as $director) {
        $nameDirector = explode(' ', trim($director));

        $directorIds[] = createDirectorAndGetId($nameDirector[0], $nameDirector[1] ?? '', $db);
    }

    //Créer le film dans la BDD
    $movieId = createMovie($title, $country, $release_year, $description, $duration, $db);


    //Associer les films et les réalisateurs
    foreach(array_unique($directorIds) as $directorId) {
        try {
            $associateQuery = "INSERT INTO movie_director (director_id, movie_id) VALUES (:dir_id, :mov_id);";
            $associateStatement = $db->prepare($associateQuery);
            $associateStatement->bindParam(':dir_id', $directorId, PDO::PARAM_INT);
            $associateStatement->bindParam(':mov_id', $movieId, PDO::PARAM_INT);

            $associateStatement->execute();
        } catch(Exception $ex) {
            echo "BUG : sur le film $title\n";
        }
    }
    
}

//fermer la connexion au CSV
fclose($handle);


function createDirectorAndGetId(?string $firstname, ?string $lastname, PDO $connection) : int
{

    //Vérifier si le réalisateur existe déjà
    $checkQuery = "SELECT id FROM director WHERE firstname = :firstname AND lastname = :lastname;";
    $checkStatement = $connection->prepare($checkQuery);
    $checkStatement->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $checkStatement->bindParam(':lastname', $lastname, PDO::PARAM_STR);

    $checkStatement->execute();
    $result = $checkStatement->fetchAll();

    if(count($result) == 0) {
        //Créer le real
        $sqlDirectorQuery = "INSERT INTO director (firstname, lastname) VALUES (:firstname, :lastname);";
        $directorStatement = $connection->prepare($sqlDirectorQuery);
        $directorStatement->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $directorStatement->bindParam(':lastname', $lastname, PDO::PARAM_STR);

        $directorStatement->execute();

        return createDirectorAndGetId($firstname, $lastname, $connection);
    } else {
        //renvoyer son id
        return $result[0]['id'];
    }
}

function createMovie($title, $country, $release_year, $description, $duration, PDO $connection) : int 
{
    //Envoyer la requete de creation de film
    $sqlQuery = "INSERT INTO movie (title, country, release_year, description, duration) VALUES (:title, :country, :release_year, :description, :duration);";
    $statement = $connection->prepare($sqlQuery);
    $statement->bindParam(':title', $title, PDO::PARAM_STR);
    $statement->bindParam(':country', $country, PDO::PARAM_STR);
    $statement->bindParam(':release_year', $release_year, PDO::PARAM_INT);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    $statement->bindParam(':duration', $duration, PDO::PARAM_INT);

    $statement->execute();

    //Recupérer l'id du film créer
    $checkQuery = "SELECT id FROM movie WHERE title = :title AND release_year = :release_year";
    $checkStatement = $connection->prepare($checkQuery);
    $checkStatement->bindParam(':title', $title, PDO::PARAM_STR);
    $checkStatement->bindParam(':release_year', $release_year, PDO::PARAM_INT);

    $checkStatement->execute();
    $result = $checkStatement->fetchAll();

    return $result[0]['id'];

}