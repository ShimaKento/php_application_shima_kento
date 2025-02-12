<!doctype html>
<html lang="ja">

<head>
  <link rel="stylesheet" href="style.css">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title>ログイン画面</title>
</head>

<body>

  <?php


  try {
    $dsn = 'mysql:host=localhost;dbname=twitter;charset=utf8';
    $username = 'root';
    $password = '';
    $option = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $option);
    //echo "データベースに接続できました！".'<br>'.'<br>';
  } catch (PDOException $e) {
    echo "接続に失敗しました。" . $e->getMessage();
  }

  session_start();

  $logout = 0;

  if (!empty($_SESSION['id'])) {
    // セッションを終了する
    $_SESSION = [];
    $logout = 1;
  }

  /*家で開発するなら、「https://drive.google.com/file/d/1Aj5yW2z6d-kjGtdr0krFqf4OckCtpapc/view?usp=drive_link」と
「https://drive.google.com/file/d/11LAlt7olr1TTWwooxmz0JPAUBWjWowI3/view?usp=drive_link」をmySQLにダウンロードする

javascriptでメアドとパスワードがあっている時のみログインできるようにし、
エラー文も出るようにする。*/

  ?>

  <header class="stels">
  </header>
  <main>
    <?php if ($logout === 1) {
      echo 'ログアウトしました。';
    } ?>
    <form id="login" action="article.php" method="post">
      <div class="cover">
        <section class="set">
          <p>メールアドレス:</p>
          <input type="email" name="email" value="">
        </section>
        <section class="set">
          <p>パスワード:</p>
          <input type="password" name="password" value="">
        </section>
        <section class="set">
          <input type="submit" value="送信">
        </section>
        <div class="btn">
          <div class="cover">
            <label for="checkPassword" class="fa fa-eye"></label>
            <label for="checkPassword" class="fa fa-eye-slash"></label>
          </div>
        </div>
        <?php
        if (!empty($_GET["redirect"])) {
          if ($_GET["redirect"] === "redirect") {
            echo '<p class="red">正しい組み合わせのメールアドレスとパスワードを<br>入力してください</p>';
            $_GET["redirect"] = [];
          }
        }
        ?>
      </div>
    </form>
    <a class="addlink" href="user_add.php">新規登録</a>
  </main>
  <footer>
  </footer>
  <script src="login.js" defer></script>

</body>

</html>