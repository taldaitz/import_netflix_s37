<?php

use Dawan\ImportNetflix\Entity\Movie;
use PHPUnit\Framework\TestCase;

class MovieTest extends TestCase
{
    public function testInstanciate()
    {
        $csvLine = explode(';', 's7309;Movie;Lincoln;Steven Spielberg;Daniel Day-Lewis, Sally Field, David Strathairn, Joseph Gordon-Levitt, James Spader, Hal Holbrook, Tommy Lee Jones, Jackie Earle Haley, John Hawkes, Jared Harris, Joseph Cross, Tim Blake Nelson, David Oyelowo, Bruce McGill;United States, India;February 21;2018;2012;PG-13;150 min;Dramas;Director Steven Spielberg takes on the towering legacy of Abraham Lincoln, focusing on his stewardship of the Union during the Civil War years.;;;;;');

        $movie = new Movie($csvLine);

        $this->assertEquals('Lincoln', $movie->title);
        $this->assertEquals(2012, $movie->release_year);
    }
}