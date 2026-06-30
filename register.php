<?php
require 'db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'ユーザー名とパスワードを入力してください。';
    }

    if (empty($errors)) {
        // check exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'そのユーザー名は既に使われています。';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: mypage.php');
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>新規登録 | CineLog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">🎬 CineLog <span>シネログ</span></div>
    <nav>
        <a href="index.php">ホーム</a>
        <a href="movies.php">レビュー一覧</a>
    </nav>
</header>

<section class="form-section">
    <h1>アカウント作成</h1>
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?><p>⚠️ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="register.php">
        <label>ユーザー名</label>
        <input name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <label>パスワード</label>
        <input name="password" type="password" required>
        <button type="submit">登録</button>
    </form>
    <p>既にアカウントがある場合は <a href="login.php">ログイン</a></p>
</section>
</body>
</html>
