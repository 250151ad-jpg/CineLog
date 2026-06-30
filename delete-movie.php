<?php

require 'db.php';

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {

    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$id]);

}

header("Location: movies.php");
exit;