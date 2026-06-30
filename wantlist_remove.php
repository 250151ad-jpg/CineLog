<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['movie_id']) || !ctype_digit($_GET['movie_id'])) {
    header('Location: movies.php');
    exit;
}

$movie_id = (int)$_GET['movie_id'];

$stmt = $pdo->prepare("DELETE FROM user_wantlist WHERE user_id = ? AND movie_id = ?");
$stmt->execute([(int)$_SESSION['user_id'], $movie_id]);

$redirect = $_SERVER['HTTP_REFERER'] ?? 'movies.php';
header('Location: ' . $redirect);
exit;

?>
