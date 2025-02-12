<!doctype html>
<html lang="ja">

<head>
  <link rel="stylesheet" href="style.css">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title>新規登録</title>
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
  //usersに各種情報を追加するクエリを作る。

  ?>

  <header class="stels"></header>
  <main>
    <?php

    $formTemplate = '<form id="userAdd" class="userAdd" action="" method="post">
      <div class="cover">
        <section class="set">
          <p>メールアドレス:</p>
          <input type="email" name="email" value="">
          <p class="red"></p>
        </section>
        <section class="set">
          <p>パスワード:</p>
          <input type="password" name="password" value="">
          <p class="red"></p>
        </section>
        <section class="set">
          <p>再入力:</p>
          <input type="password" name="password2" value="">
        </section>
        <section class="set">
          <p>名字:</p>
          <input type="text" name="first_name" value="">
          <p class="red"></p>
        </section>
        <section class="set">
          <p>氏名:</p>
          <input type="text" name="last_name" value="">
          <p class="red"></p>
        </section>
        <section class="set">
          <input type="button" value="送信">
        </section>
        <section class="btn">
          <div class="cover">
            <label for="checkPassword" class="fa fa-eye"></label>
            <label for="checkPassword" class="fa fa-eye-slash"></label>
          </div>
        </section>
      </div>
    </form>';

    //$_POST[]で条件分岐を行ない、未入力なら入力フォームを出し、入力してたら以下の処理を行なう。
    if (empty($_POST["email"])) {
      echo $formTemplate;
    } else {
      $alradyEmails = $pdo->query('SELECT email FROM twitter.users');
      $diffarCount = 0;
      while ($alradyEmail = $alradyEmails->fetch()) {
        if ($_POST["email"] === $alradyEmail['email']) {
          $diffarCount++;
        }
      }

      if ($diffarCount > 0) {
        echo '<div class="cover"><h1>このメールアドレスは既に登録されています。<br>
  別のメールアドレスを記載して下さい。</h1></div>';
        echo $formTemplate;
      } else {
        //ダブりがなければアカウントを登録(sqlにデータを保存する)

        //インサート文を変数に格納する(変数は''で書く。””はエラーが発生する)。
        $insert = 'INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name,:last_name,:email,:password)';
        //値が空のままのSQL文をprepare()にセットし、SQL実行のための準備を行う
        $account = $pdo->prepare($insert);
        //実際に挿入する値を配列に格納
        $params = array(':first_name' => $_POST["first_name"], ':last_name' => $_POST["last_name"], ':email' => $_POST["email"], ':password' => hash('sha256', $_POST["password"]));

        //そしてexecute()に値が入った配列をセットしてSQLを実行し、データベースにデータを挿入する
        $account->execute($params);




        //登録したメアドのIDを取得し、ログイン出来るようにする。

        echo '<h1 class="alert">登録！</h1>';
        echo '<form class="bordLink" action="article.php" method="post">
    <p>今すぐ掲示板に投稿しますか？</p>
    <input type="text" class="stels" name="email" value="' . htmlentities($_POST["email"]) . '"><input type="text" class="stels" name="password" value="' . htmlentities($_POST["password"]) . '"><input type="submit" value="投稿しに行く！">
  </form>';/**/
      }
    }

    ?>
  <a class="addlink" href="login.php">ログインページに戻る</a>
  </main>
  <footer>
  </footer>
  <script src="user_add.js" defer></script>

</body>

</html>