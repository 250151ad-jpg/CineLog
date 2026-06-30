<?php

require 'db.php';

// Validate ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: movies.php");
    exit;
}

$id = (int)$_GET['id'];

$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title   = trim($_POST['title'] ?? '');
    $genre   = $_POST['genre'] ?? '';
    $rating  = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    // Validation
    if ($title === '') {
        $errors[] = 'タイトルを入力してください。';
    }
    if ($comment === '') {
        $errors[] = '感想を入力してください。';
    }
    if ($rating < 1 || $rating > 5) {
        $errors[] = '評価を選択してください。';
    }

    if (empty($errors)) {
        // Ensure movies table has `image` column (non-fatal)
        try {
            $pdo->exec("ALTER TABLE movies ADD COLUMN image VARCHAR(255) NULL");
        } catch (Exception $e) {}

        $imageName = null;
        if (!empty($_FILES['image']) && isset($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $orig = basename($_FILES['image']['name']);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $imageName = time() . '_' . bin2hex(random_bytes(6)) . ($ext ? '.' . $ext : '');
            $dest = __DIR__ . '/uploads/' . $imageName;
            if (!move_uploaded_file($tmp, $dest)) {
                $imageName = null;
            }
        }

        if ($imageName) {
            // Remove previous image file if exists and different
            if (!empty($movie['image']) && $movie['image'] !== $imageName) {
                $oldPath = __DIR__ . '/uploads/' . $movie['image'];
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $stmt = $pdo->prepare(
                "UPDATE movies SET title = ?, genre = ?, rating = ?, comment = ?, image = ? WHERE id = ?"
            );
            $stmt->execute([$title, $genre, $rating, $comment, $imageName, $id]);
        } else {
            $stmt = $pdo->prepare(
                "UPDATE movies SET title = ?, genre = ?, rating = ?, comment = ? WHERE id = ?"
            );
            $stmt->execute([$title, $genre, $rating, $comment, $id]);
        }

        header("Location: movies.php");
        exit;
    }
}

// Fetch current movie data
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    header("Location: movies.php");
    exit;
}

// If POST had errors, use submitted values; otherwise use DB values
$formTitle   = $_POST['title']   ?? $movie['title'];
$formGenre   = $_POST['genre']   ?? $movie['genre'];
$formRating  = isset($_POST['rating']) ? (int)$_POST['rating'] : (int)$movie['rating'];
$formComment = $_POST['comment'] ?? $movie['comment'];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>映画編集 | CineLog</title>
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

<section class="form-section">
    <h1>映画を編集</h1>

    <?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach($errors as $error): ?>
        <p>⚠️ <?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="edit-movie.php?id=<?= $id ?>" enctype="multipart/form-data">

        <label for="title">タイトル</label>
        <input
            type="text"
            id="title"
            name="title"
            value="<?= htmlspecialchars($formTitle) ?>"
            required
        >

        <label for="genre">ジャンル</label>
        <select id="genre" name="genre">
            <?php
            $genres = ["SF","Action","Drama","Anime","Comedy"];
            foreach ($genres as $g):
                $selected = ($formGenre === $g) ? 'selected' : '';
            ?>
            <option value="<?= $g ?>" <?= $selected ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>

        <label for="rating">評価</label>
        <select id="rating" name="rating">
            <?php for ($i = 5; $i >= 1; $i--):
                $selected = ($formRating === $i) ? 'selected' : '';
            ?>
            <option value="<?= $i ?>" <?= $selected ?>><?= str_repeat("★", $i) ?></option>
            <?php endfor; ?>
        </select>

        <label for="comment">感想</label>
        <textarea
            id="comment"
            name="comment"
            rows="5"
            required
        ><?= htmlspecialchars($formComment) ?></textarea>

        <label for="image">ポスター画像 (任意)</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">更新する</button>

    </form>
</section>

</body>
</html>