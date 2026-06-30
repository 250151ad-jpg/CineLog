<?php

require 'db.php';

if (isset($_GET['search']) && $_GET['search'] !== '') {

    $search = "%" . $_GET['search'] . "%";
    $stmt   = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY id DESC");
    $stmt->execute([$search]);

} else {

    $stmt = $pdo->query("SELECT * FROM movies ORDER BY id DESC");

}

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
        <span>シネログ</span>
    </div>
    <nav>
        <a href="index.php">ホーム</a>
        <a href="movies.php">レビュー一覧</a>
        <a href="add-movie.php">映画登録</a>
    </nav>
</header>

<section class="reviews">

    <h1 class="section-title">🎞 レビュー一覧</h1>

    <section class="search-section">
        <form action="movies.php" method="GET">
            <input
                type="text"
                name="search"
                placeholder="映画タイトルを検索..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
            >
            <button type="submit">🔍 検索</button>
        </form>
    </section>

    <div style="text-align:center; margin-bottom:30px;">
        <a href="movies.php" class="hero-btn">🎬 全ての映画を見る</a>
    </div>

    <div class="review-grid">

        <?php if (count($movies) > 0): ?>

            <?php foreach ($movies as $movie): ?>
            <div class="card">
                <h3><?= htmlspecialchars($movie['title']) ?></h3>
                <div class="genre"><?= htmlspecialchars($movie['genre']) ?></div>
                <div class="stars"><?= str_repeat("★", (int)$movie['rating']) ?></div>
                <p><?= htmlspecialchars($movie['comment']) ?></p>
                <div class="card-buttons">
                    <a href="edit-movie.php?id=<?= (int)$movie['id'] ?>" class="edit-btn">✏️ 編集</a>
                    <a
                        href="delete-movie.php?id=<?= (int)$movie['id'] ?>"
                        class="delete-btn"
                        onclick="return confirm('この映画を削除しますか？');"
                    >🗑️ 削除</a>
                </div>
            </div>
            <?php endforeach; ?>

        <?php else: ?>

            <p style="text-align:center;font-size:20px;color:#9ca3af;">
                <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
                    「<?= htmlspecialchars($_GET['search']) ?>」に一致する映画はありません。
                <?php else: ?>
                    まだ映画が登録されていません。
                <?php endif; ?>
            </p>

        <?php endif; ?>

    </div>

</section>

<footer>
    <p>© 2026 CineLog</p>
    <p>星でつなぐ、映画の記録アプリ</p>
</footer>

</body>
</html>