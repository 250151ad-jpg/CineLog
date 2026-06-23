<?php

require 'db.php';

$stmt = $pdo->query(
    "SELECT * FROM movies ORDER BY id DESC"
);

$movies = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>レビュー一覧 | CineLog</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        🎬 CineLog
    </div>
</header>

<section class="reviews">

    <h1 class="section-title">
        🎞 レビュー一覧
    </h1>

    <div class="review-grid">

        <?php if(count($movies) > 0): ?>

            <?php foreach($movies as $movie): ?>

                <div class="card">

                    <h3>
                        <?= htmlspecialchars($movie['title']) ?>
                    </h3>

                    <div class="genre">
                        <?= htmlspecialchars($movie['genre']) ?>
                    </div>

                    <div class="stars">
                        <?= str_repeat("★", $movie['rating']) ?>
                    </div>

                    <p>
                        <?= htmlspecialchars($movie['comment']) ?>
                    </p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>まだ映画が登録されていません。</p>

        <?php endif; ?>

    </div>

</section>

</body>
</html>