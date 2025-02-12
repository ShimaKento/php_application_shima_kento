<!doctype html>
<html lang="ja">

<head>
  <link rel="stylesheet" href="style.css">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title>ベースキャンプ</title>
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
  //右側を入力した状態でボタンを押したとき、各要素の入力を確認し、されていればsqlに変更を加え、無ければ注意文を表示する。emailはダブって入れば変更せず、パスワードはハッシュ化する。
  //passwordは入力してもらう。

  if (!empty($_POST["newFirst_name"]) || !empty($_POST["newLast_name"]) || !empty($_POST["newEmail"]) || (!empty($_POST["newPassword"]) && !empty($_POST["againPassword"]))) {
    $infoIn = 1;
  }

  if (empty($infoIn)) {
  } else if ($infoIn === 1) {

    $users = $pdo->query('SELECT * FROM twitter.users');
    while ($user = $users->fetch()) {
      if ($_SESSION['id'] === $user['id']) {
        $firstName = htmlentities($user['first_name']);

        if (!empty($_POST["newFirst_name"])) {
          $newFirstName = $_POST["newFirst_name"];
          $firstnameCange = 1;
        } else {
          $newFirstName = $user['first_name'];
        }

        if (!empty($_POST["newLast_name"])) {
          $newLastName = $_POST["newLast_name"];
          $lastnameCange = 1;
        } else {
          $newLastName = $user['last_name'];
        }

        if (!empty($_POST["newEmail"])) {
          $newEmail = $_POST["newEmail"];
          $emailCange = 1;
        } else {
          $newEmail = $user['email'];
        }

        if (!empty($_POST["password"]) && !empty($_POST["newPassword"]) && !empty($_POST["againPassword"])) {
          if ($_POST["newPassword"] === $_POST["againPassword"]) {
            if (hash('sha256', $_POST["password"]) === $user['password']) {
              $newPassword = hash('sha256', $_POST["newPassword"]);
              $passCange = 1;
            } else {
              $newPassword = $user['password'];
              $passCange = 2;
            }
          } else {
            $newPassword = $user['password'];
            $passCange = 3;
          }
        } else {
          $newPassword = $user['password'];
        }
      }
    }




    $insert = 'UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, password = :password WHERE id = :id ';

    $account = $pdo->prepare($insert);

    $params = array(':first_name' => $newFirstName, ':last_name' => $newLastName, ':email' => $newEmail, ':password' => $newPassword, ':id' => $_SESSION['id']);

    $account->execute($params);
    $_POST = [];
  }
  ?>

  <header>
    <h2>
      <?php
      $users = $pdo->query('SELECT * FROM twitter.users');
      while ($user = $users->fetch()) {
        if ($_SESSION['id'] === $user['id']) {
          $firstName = htmlentities($user['first_name']);
          $lastName = htmlentities($user['last_name']);
          echo 'ようこそ' . $firstName . "\n" . $lastName . 'さん！';
          $firstName = [];
          $lastName = [];
        }
      }
      ?>
    </h2>
    <section class="userRelations">
      <h3>
        <form action="login.php" method="post">
          <input type="submit" value="ログアウト">
        </form>
      </h3>
    </section>
  </header>
  <main>
    <form action="user_custom.php" class="edit" method="post">
      <?php
      $users = $pdo->query('SELECT * FROM twitter.users');
      if (empty($infoIn)) {
        echo '<section id="userCustom">';
        while ($user = $users->fetch()) {
          if ($_SESSION['id'] === $user['id']) {
            echo '<input class="item firstName" type="button" value="名字 :">
            <input type="text" class="prev firstName" name="first_name" value="' . htmlentities($user['first_name']) . '">
            <input type="text" class="next firstName" name="newFirst_name" value="">

            <input class="item lastName" type="button" value="氏名 :">
            <input type="text" class="prev lastName" name="last_name" value="' . htmlentities($user['last_name']) . '">
            <input type="text" class="next lastName" name="newLast_name" value="">

            <input class="item email" type="button" value="メールアドレス :">
            <input type="email" class="prev email" name="email" value="' . htmlentities($user['email']) . '">
            <input type="email" class="next email" name="newEmail" value="">

            <input class="item password" type="button" value="パスワード :">
            <input class="prev password" type="password" name="password" value="">
            <input class="center password" type="password" name="newPassword" value="">
            <input class="end password" type="password" name="againPassword" value="">
            <div class="btn">
              <div class="cover">
                <label for="checkPassword" class="fa fa-eye"></label>
                <label for="checkPassword" class="fa fa-eye-slash"></label>
              </div>
            </div>

            <div class="text prev">今のパスワードを入力してください</div>
            <div class="text center">新規のパスワードを入力してください</div>
            <div class="text end">再入力</div>
            <input type="submit" class="change" value="変更する"></section>';
          }
        }
      } else if ($infoIn === 1) {
        echo '<section class="changeSuccess">';
        if (!empty($firstnameCange)) {
          echo '<h1>名字を変更しました。</h1>';
        }
        if (!empty($lastnameCange)) {
          echo '<h1>氏名を変更しました。</h1>';
        }
        if (!empty($emailCange)) {
          echo '<h1>メールアドレスを変更しました。</h1>';
        }
        if (!empty($passCange)) {
          if ($passCange === 1) {
            echo '<h1>パスワードを変更しました。</h1>';
          } else if ($passCange === 2) {
            echo '<h1>旧パスワードが一致しなかったので、パスワード変更に失敗しました。</h1>';
          } else if ($passCange === 3) {
            echo '<h1>新規パスワードと再入力が一致しなかったので、パスワード変更に失敗しました。</h1>';
          }
        }
        echo '</section>';
      }
      ?>

    </form>
    <form action="article.php" class="bordView" method="post">
      <input type="submit" value="掲示板を見に行く">
    </form>
  </main>
  <footer>
  </footer>
  <script src="user_custom.js" defer></script>

</body>

</html>