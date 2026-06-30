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
// Ensure wantlist table exists
try {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS wantlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            movie_id INT NOT NULL UNIQUE,
            added_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
    // Users table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
    // Per-user wantlist mapping
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS user_wantlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            movie_id INT NOT NULL,
            added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY user_movie_unique (user_id, movie_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
    // Add image column to movies if missing (non-fatal)
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN image VARCHAR(255) NULL");
    } catch (PDOException $e) {
        // ignore if column already exists or table missing
    }
} catch (PDOException $e) {
    // Non-fatal: continue without crashing the app
}
?>