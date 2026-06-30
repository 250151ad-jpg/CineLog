<?php
require 'db.php';

// Simple AI recommendation: top rated movies
$stmt = $pdo->query("SELECT * FROM movies ORDER BY rating DESC, id DESC LIMIT 6");
$recs = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIおすすめ | CineLog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">🎬 CineLog <span>シネログ</span></div>
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
    <h1 class="section-title">🤖 AIおすすめ</h1>
    <p style="color:#6b7280;">あなたのレビュー履歴からの簡易おすすめ（評価の高い作品を表示）</p>

    <div class="movie-grid">
        <?php if (count($recs) > 0): ?>
            <?php foreach ($recs as $movie): ?>
                <?php $poster = !empty($movie['image']) ? $movie['image'] : null; ?>
                <article class="movie-card">
                    <?php if ($poster && file_exists(__DIR__.'/uploads/'.$poster)): ?>
                        <div class="poster"><img src="uploads/<?= htmlspecialchars($poster) ?>" alt="<?= htmlspecialchars($movie['title']) ?>"></div>
                    <?php else: ?>
                        <div class="poster placeholder">🎬</div>
                    <?php endif; ?>
                    <div class="movie-body">
                        <?php $title = trim($movie['title'] ?? ''); ?>
                        <h3 class="movie-title"><?= htmlspecialchars($title ?: 'タイトル未設定') ?></h3>
                        <div class="movie-meta">
                            <span class="genre"><?= htmlspecialchars($movie['genre']) ?></span>
                            <span class="stars"><?= str_repeat('★', (int)$movie['rating']) ?></span>
                        </div>
                        <p class="movie-excerpt"><?= htmlspecialchars(mb_strimwidth($movie['comment'],0,120,'...')) ?></p>
                        <div class="card-buttons">
                            <a href="wantlist_add.php?movie_id=<?= (int)$movie['id'] ?>" class="edit-btn">➕ 観たいに追加</a>
                            <a href="edit-movie.php?id=<?= (int)$movie['id'] ?>" class="edit-btn">✏️ 編集</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;color:#9ca3af;">おすすめできる映画がまだありません。</p>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>© 2026 CineLog</p>
    <p>星でつなぐ、映画の記録アプリ</p>
</footer>

</body>
</html>
