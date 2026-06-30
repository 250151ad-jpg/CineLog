<?php

require 'db.php';
$current = basename($_SERVER['PHP_SELF']);

// Recent 4 movies
$stmt = $pdo->query("SELECT * FROM movies ORDER BY id DESC LIMIT 4");
$movies = $stmt->fetchAll();

// Total number of movies
$total = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();

// Average rating
$average = $pdo->query("SELECT AVG(rating) FROM movies")->fetchColumn();
$average = $average ? round($average, 1) : 0;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineLog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        🎬 CineLog
        <span>シネログ</span>
    </div>
    <nav>
        <a href="index.php" class="<?= $current==='index.php' ? 'active' : '' ?>">ホーム</a>
        <a href="movies.php" class="<?= $current==='movies.php' ? 'active' : '' ?>">レビュー一覧</a>
        <a href="wantlist.php" class="<?= $current==='wantlist.php' ? 'active' : '' ?>">観たいリスト</a>
        <a href="ai.php" class="<?= $current==='ai.php' ? 'active' : '' ?>">AIおすすめ</a>
        <a href="mypage.php" class="<?= $current==='mypage.php' ? 'active' : '' ?>">マイページ</a>
        <a href="add-movie.php" class="<?= $current==='add-movie.php' ? 'active' : '' ?>">映画登録</a>
    </nav>
    <div class="header-right">
        <a href="add-movie.php" class="register-btn">映画を登録する</a>
        <a href="notifications.php" class="nav-icon">🔔</a>
        <a href="mypage.php" class="nav-user">👤 Mahaju</a>
    </div>
</header>

<section class="hero">
    <div class="hero-text">
        <p class="subtitle">星でつなぐ、映画の記録アプリ</p>
        <h1>CineLog</h1>
        <p class="description">
            観た映画を星評価と一言で記録。
            レビューが増えるほどAIがあなたの好みを分析し、
            おすすめ映画を提案します。
        </p>
        <div class="hero-buttons">
            <a href="movies.php" class="hero-btn">🎞 レビューを見る</a>
            <a href="add-movie.php" class="hero-btn outline-btn">＋ 映画を登録する</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="images/reel.png" alt="Movie Reel">
    </div>
</section>

<section class="search-section">
    <form action="movies.php" method="GET">
        <div class="search-input-wrap">
            <input type="text" name="search" placeholder="映画タイトルを検索...">
            <button class="search-btn" type="submit" aria-label="検索">🔍</button>
        </div>
        <div class="search-tags">
            <a href="movies.php?search=SF">#SF</a>
            <a href="movies.php?search=アクション">#アクション</a>
            <a href="movies.php?search=2026">#2026年新作</a>
            <a href="movies.php?search=アニメ">#アニメ</a>
        </div>
    </form>
</section>

<section class="reviews">
    <div class="section-title">⭐ 最近のレビュー</div>
    <div class="review-grid">
        <?php foreach($movies as $movie): ?>
        <?php
            $title = trim($movie['title'] ?? '');
            $poster = (!empty($movie['image']) && file_exists(__DIR__ . '/uploads/' . $movie['image'])) ? 'uploads/' . rawurlencode($movie['image']) : null;
        ?>
        <article class="movie-card">
            <?php if ($poster): ?>
                <div class="poster"><img src="<?= htmlspecialchars($poster) ?>" alt="<?= htmlspecialchars($title ?: '映画ポスター') ?>"></div>
            <?php else: ?>
                <div class="poster placeholder">🎬</div>
            <?php endif; ?>
            <div class="movie-body">
                <h3 class="movie-title"><?= htmlspecialchars($title ?: 'タイトル未設定') ?></h3>
                <div class="movie-meta">
                    <span class="genre"><?= htmlspecialchars($movie['genre']) ?></span>
                </div>
                <div class="movie-meta">
                    <span class="stars"><?= str_repeat('★', (int)$movie['rating']) ?></span>
                </div>
                <p class="movie-excerpt"><?= htmlspecialchars(mb_strimwidth($movie['comment'], 0, 120, '...')) ?></p>
            </div>
        </article>
        <?php endforeach; ?>
        <?php if(count($movies) === 0): ?>
        <p style="text-align:center;font-size:18px;color:#9ca3af;">まだ映画が登録されていません。</p>
        <?php endif; ?>
    </div>
</section>

<section class="stats">
    <div class="stat-box">
        🎬
        <h3><?= $total ?>本</h3>
        <p>総鑑賞作品数</p>
    </div>
    <div class="stat-box">
        ⭐
        <h3><?= $average ?></h3>
        <p>平均評価</p>
    </div>
    <div class="stat-box">
        🏷️
        <h3>SF・アクション</h3>
        <p>お気に入りジャンル</p>
    </div>
    <div class="stat-box">
        📅
        <h3>2026.06.22</h3>
        <p>登録日</p>
    </div>
</section>

<section class="personality">
    <h2>🎭 あなたのシネマ・パーソナリティ</h2>
    <div class="personality-card">
        <div class="personality-badge" aria-hidden="true">
            <!-- Astronaut helmet SVG badge -->
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill="none">
                <rect x="4" y="12" width="56" height="40" rx="8" fill="#0b1220" />
                <path d="M16 22c2-6 10-8 18-8s16 2 18 8v12c-2 6-10 8-18 8s-16-2-18-8V22z" fill="#162134" />
                <circle cx="32" cy="30" r="10" fill="#9fb8ff" />
                <path d="M24 26c2-3 8-4 12-4" stroke="#0b1220" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <div class="personality-chart" role="img" aria-label="ジャンル内訳">
            <canvas id="personalityChart" width="110" height="110"></canvas>
        </div>
        <div class="personality-body">
            <h3 class="personality-title">SF探検家型</h3>
            <p class="personality-desc">あなたは壮大な世界観と深いテーマを持つSF映画を好む傾向があります。</p>
            <a href="movies.php" class="ai-btn">おすすめ映画を見る</a>
        </div>
    </div>
</section>

<!-- Chart.js (doughnut) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function(){
        const ctx = document.getElementById('personalityChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['SF','アクション','ドラマ'],
                datasets: [{
                    data: [80,15,5],
                    backgroundColor: ['#60a5fa','#fbbf24','#a78bfa'],
                    hoverOffset: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: false,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
    })();
</script>

<footer>
    <p>© 2026 CineLog</p>
    <p>星でつなぐ、映画の記録アプリ</p>
</footer>

</body>
</html>