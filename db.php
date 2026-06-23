<?php

$host = "localhost";
$dbname = "cinelog";
$username = "root";
$password = "";

try{

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

}catch(PDOException $e){

    die("Connection Failed: " . $e->getMessage());

}
?>