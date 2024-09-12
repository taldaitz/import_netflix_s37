<?php

require 'vendor/autoload.php';



use Dawan\ImportNetflix\Entity\Director;
use Dawan\ImportNetflix\Entity\Movie;

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
    $movie = new Movie($line);

    //extraire les informations d'un réalisateur
    $directors = Director::getDirectorsFromCsvLine($line[3]);

    $movie->save($db);

    foreach($directors as $director) {
        $director->save();
        $director->associateMovie();
    }    
}

//fermer la connexion au CSV
fclose($handle);
