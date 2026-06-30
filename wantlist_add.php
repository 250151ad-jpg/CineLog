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

// Check movie exists
$stmt = $pdo->prepare("SELECT id FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$exists = $stmt->fetchColumn();

if ($exists) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO user_wantlist (user_id, movie_id) VALUES (?, ?)");
    $stmt->execute([(int)$_SESSION['user_id'], $movie_id]);
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'movies.php';
header('Location: ' . $redirect);
exit;

?>
