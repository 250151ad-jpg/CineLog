<?php

require 'db.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO movies
            (title, genre, rating, comment)
            VALUES
            (?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $title,
        $genre,
        $rating,
        $comment
    ]);

    echo "登録成功！";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>映画登録 | CineLog</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">🎬 CineLog</div>
</header>

<section class="form-section">

    <h1>映画を登録する</h1>

    <form method="POST">

        <label>映画タイトル</label>
        <input type="text" name="title" placeholder="Interstellar">

        <label>ジャンル</label>
        <select name="genre">
            <option>SF</option>
            <option>Action</option>
            <option>Drama</option>
            <option>Anime</option>
            <option>Comedy</option>
        </select>

        <label>評価</label>

       <select name="rating">
            <option value="5">★★★★★</option>
            <option value="4">★★★★</option>
            <option value="3">★★★</option>
            <option value="2">★★</option>
            <option value="1">★</option>
        </select>

        <label>感想</label>

        <textarea
            name="comment"
            rows="5"
            placeholder="映画の感想を書いてください"></textarea>
        <button type="submit">
            登録する
        </button>

    </form>

</section>

</body>
</html>