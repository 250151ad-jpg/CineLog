<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$total = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$wantcountStmt = $pdo->prepare("SELECT COUNT(*) FROM user_wantlist WHERE user_id = ?");
$wantcountStmt->execute([(int)$_SESSION['user_id']]);
$wantcount = $wantcountStmt->fetchColumn();
$average = $pdo->query("SELECT AVG(rating) FROM movies")->fetchColumn();
$average = $average ? round($average,1) : 0;

// Top genres
$stmt = $pdo->query("SELECT genre, COUNT(*) as c FROM movies GROUP BY genre ORDER BY c DESC LIMIT 5");
$genres = $stmt->fetchAll();

// Wantlist preview
$wlStmt = $pdo->prepare("SELECT m.* FROM movies m JOIN user_wantlist w ON m.id = w.movie_id WHERE w.user_id = ? ORDER BY w.added_at DESC LIMIT 4");
$wlStmt->execute([(int)$_SESSION['user_id']]);
$wantPreview = $wlStmt->fetchAll();

// Recent reviews (global)
$recentStmt = $pdo->query("SELECT * FROM movies ORDER BY id DESC LIMIT 4");
$recentReviews = $recentStmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ | CineLog</title>
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
    <div style="margin-left:20px;">
        <span style="color:#d1d5db; margin-right:12px;">ようこそ、<?= htmlspecialchars($_SESSION['username'] ?? 'ユーザー') ?>さん</span>
        <a href="logout.php" class="logout-btn">ログアウト</a>
    </div>
</header>

<!-- Hero banner -->
<section class="hero mypage">
    <div class="hero-content">
        <h1>🎬 おかえり、<?= htmlspecialchars($_SESSION['username'] ?? 'ユーザー') ?>さん！</h1>
        <p class="hero-sub">映画を記録して、新しいお気に入りを見つけよう。</p>
        <div class="hero-buttons">
            <a href="ai.php" class="hero-btn">AIおすすめ</a>
            <a href="wantlist.php" class="hero-btn outline-btn">観たいリスト</a>
        </div>
    </div>
</section>

<main class="mypage-container">
    <section class="profile-card">
        <div class="profile-left">
            <div class="avatar">🎬</div>
            <div>
                <h2><?= htmlspecialchars($_SESSION['username'] ?? 'ユーザー') ?></h2>
                <p class="muted">映画の記録を楽しんでください</p>
            </div>
        </div>
        <!-- Removed duplicate action buttons (hero already provides quick actions) -->
    </section>

    <section class="stats">
        <div class="card">
            <div class="stat-emoji">🎬</div>
            <div class="stat-value"><?= $total ?></div>
            <div class="stat-label">総鑑賞作品数</div>
        </div>
        <div class="card">
            <div class="stat-emoji">⭐</div>
            <div class="stat-value"><?= $average ?></div>
            <div class="stat-label">平均評価</div>
        </div>
        <div class="card">
            <div class="stat-emoji">📌</div>
            <div class="stat-value"><?= $wantcount ?></div>
            <div class="stat-label">観たいリスト</div>
        </div>
        <div class="card">
            <div class="stat-emoji">🏷️</div>
            <div class="stat-value"><?= htmlspecialchars($genres[0]['genre'] ?? '-') ?></div>
            <div class="stat-label">よく見るジャンル</div>
        </div>
    </section>

    <section class="previews">
        <div class="preview-col">
            <h3>観たいリスト (プレビュー)</h3>
            <div class="preview-row">
                <?php if (count($wantPreview) > 0): ?>
                    <?php foreach ($wantPreview as $m): ?>
                        <?php $poster = !empty($m['image']) ? $m['image'] : null; ?>
                        <div class="preview-item">
                            <?php if ($poster && file_exists(__DIR__.'/uploads/'.$poster)): ?>
                                <div class="preview-poster"><img src="uploads/<?= htmlspecialchars($poster) ?>" alt="<?= htmlspecialchars($m['title'] ?? '') ?>"></div>
                            <?php else: ?>
                                <div class="preview-poster">🎬</div>
                            <?php endif; ?>
                            <div class="preview-meta"><?= htmlspecialchars(trim($m['title'] ?? '') ?: 'タイトル未設定') ?><div style="font-size:12px;color:#9ca3af;"> <?= htmlspecialchars($m['genre'] ?? '') ?></div></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="muted">観たい映画がまだありません。</p>
                <?php endif; ?>
            </div>
            <div style="margin-top:12px;"><a href="wantlist.php" class="hero-btn outline-btn">全て見る</a></div>
        </div>

        <div class="preview-col">
            <h3>最近のレビュー</h3>
            <div class="preview-row">
                <?php if (count($recentReviews) > 0): ?>
                    <?php foreach ($recentReviews as $m): ?>
                        <?php $poster2 = !empty($m['image']) ? $m['image'] : null; ?>
                        <div class="preview-item">
                            <?php if ($poster2 && file_exists(__DIR__.'/uploads/'.$poster2)): ?>
                                <div class="preview-poster"><img src="uploads/<?= htmlspecialchars($poster2) ?>" alt="<?= htmlspecialchars($m['title'] ?? '') ?>"></div>
                            <?php else: ?>
                                <div class="preview-poster">🎬</div>
                            <?php endif; ?>
                            <div class="preview-meta"><?= htmlspecialchars(trim($m['title'] ?? '') ?: 'タイトル未設定') ?><div style="font-size:12px;color:#9ca3af;"> <?= str_repeat('★', (int)$m['rating']) ?></div></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="muted">まだレビューがありません。</p>
                <?php endif; ?>
            </div>
            <div style="margin-top:12px;"><a href="movies.php" class="hero-btn outline-btn">レビュー一覧</a></div>
        </div>
    </section>

    <section style="margin-top:28px; display:flex; gap:24px; align-items:flex-start;">
        <div style="flex:0 0 320px;">
            <h3>目標の進捗</h3>
            <?php $goal = 100; $percent = ($total && $goal>0) ? min(100, round($total / $goal * 100)) : 0; ?>
            <div class="progress-wrapper">
                <div class="progress-circle" style="--p:<?= $percent ?>;"></div>
                <div>
                    <div style="font-weight:bold;">目標 <?= $goal ?> 本</div>
                    <div class="muted"><?= $total ?> / <?= $goal ?></div>
                    <div style="margin-top:8px;" class="progress-line" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $percent ?>">
                        <div class="progress-fill" style="width:<?= $percent ?>%;"></div>
                    </div>
                    <div class="progress-meta">
                        <div class="muted"><?= $total ?> / <?= $goal ?> 本</div>
                        <div style="font-weight:bold; margin-left:auto;"><?= $percent ?>%</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="flex:1;">
            <h3>最近の活動</h3>
            <div class="timeline">
                <?php if (count($recentReviews)>0): ?>
                    <?php $labels = ['今日','昨日','先週','以前']; $i=0; foreach($recentReviews as $r): ?>
                        <div class="timeline-item">
                            <strong><?= $labels[$i] ?? '以前' ?></strong>
                            — <?= str_repeat('★', (int)$r['rating']) ?> <?= htmlspecialchars($r['title'] ?: 'タイトル未設定') ?>
                        </div>
                    <?php $i++; endforeach; ?>
                <?php else: ?>
                    <p class="muted">活動はまだありません。</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<footer style="margin-top:40px; text-align:center; color:#9ca3af;">© 2026 CineLog</footer>

</body>
</html>
