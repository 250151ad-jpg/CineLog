<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT m.* FROM movies m JOIN user_wantlist w ON m.id = w.movie_id WHERE w.user_id = ? ORDER BY w.added_at DESC"
);
$stmt->execute([(int)$_SESSION['user_id']]);
$movies = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>観たいリスト | CineLog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        🎬 CineLog
        <span>シネログ</span>
    </div>
    <nav>
        <a href="index.php">ホーム</a>
        <a href="movies.php">レビュー一覧</a>
        <a href="wantlist.php">観たいリスト</a>
        <a href="ai.php">AIおすすめ</a>
        <a href="mypage.php">マイページ</a>
        <a href="add-movie.php">映画登録</a>
    </nav>
</header>

<section class="reviews">
    <h1 class="section-title">🎯 観たいリスト</h1>
    <div class="review-grid">
        <?php if (count($movies) > 0): ?>
            <?php foreach ($movies as $movie): ?>
                <div class="card">
                    <?php $title = trim($movie['title'] ?? ''); ?>
                    <h3><?= htmlspecialchars($title ?: 'タイトル未設定') ?></h3>
                    <div class="genre"><?= htmlspecialchars($movie['genre']) ?></div>
                    <p><?= htmlspecialchars($movie['comment']) ?></p>
                    <div class="card-buttons">
                        <a href="wantlist_remove.php?movie_id=<?= (int)$movie['id'] ?>" class="delete-btn">➖ 外す</a>
                        <a href="edit-movie.php?id=<?= (int)$movie['id'] ?>" class="edit-btn">✏️ 編集</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;font-size:20px;color:#9ca3af;">観たい映画がまだありません。</p>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>© 2026 CineLog</p>
    <p>星でつなぐ、映画の記録アプリ</p>
</footer>

</body>
</html>
