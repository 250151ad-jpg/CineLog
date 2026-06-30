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
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: mypage.php');
            exit;
        } else {
            $errors[] = '認証に失敗しました。';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ログイン | CineLog</title>
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
    <h1>ログイン</h1>
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?><p>⚠️ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label>ユーザー名</label>
        <input name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <label>パスワード</label>
        <input name="password" type="password" required>
        <button type="submit">ログイン</button>
    </form>
    <p>アカウントがない場合は <a href="register.php">新規登録</a></p>
</section>
</body>
</html>
