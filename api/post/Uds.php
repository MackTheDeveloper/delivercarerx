

<?php
/**
 * @OA\Info(title="API Uds TEST", version="1.0.0")
 */

error_reporting(E_ALL);
ini_set('display_error', 1);

// Headers

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: POST');


//Including required files.
include_once('../../config/databaseTest.php');
include_once('../../app/Models/Post.php');
//include_once('../../app/Http/Controllers/SwaggerController.php');


// Connecting with db

$database = new DatabaseTest;
$db = $database->connect();

$post = new Post($db);

$data = $post->readPosts();

// Check if there is posts in db

if($data->rowCount()){

    $posts = [];

    //re-aggrange the posts data.
    while($row = $data->fetch(PDO::FETCH_OBJ))
    {
        print_r($row);
        //$posts[$row]
    }

} else {
    echo json_encode(['message' => ' No posts found']);
}