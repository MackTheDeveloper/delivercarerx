<?php

//Usinf to test Uds.php table in db



error_reporting(E_ALL);
ini_set('display_error', 1);

class Post
{
    //Post properties
    public $id;
    public $zip;
    public $monday_friday;
    public $saturday;

    // DB Data.
    private $connection;
    private $table = 'uds';

    public function __construct($db){
        $this->connection = $db;
    }

    //Method to read all the saved posts from db

    public function readPosts(){

        //Query for reading Uds from table.
        $query = 'SELECT
        uds.zip,
        uds.monday_friday,
        uds.saturday
        FROM '.$this->table.'
        ORDER BY
            uds.zip DESC        
        ';

        $post =  $this->connection->prepare($query);

        $post->execute();

        return $post;

    }
}