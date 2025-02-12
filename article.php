<!doctype html>
<html lang="ja">

<head>
  <link rel="stylesheet" href="style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title>掲示板</title>
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

  $users = $pdo->query('SELECT * FROM twitter.users');
  if (empty($_SESSION['id'])) {
    while ($user = $users->fetch()) {
      if (hash('sha256', $_POST["password"]) === $user['password'] && $_POST["email"] === $user['email']) {
        $_SESSION['id'] = $user['id'];
      }
    }
  }

  if (empty($_SESSION['id'])) {
    header('Location:login.php?redirect=redirect');
    exit;
  }
  //新しい順つまりはIDが大きい順に並べ替える

  ?>

  <header>
    <div class="cover">
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
          <form action="user_custom.php" method="post">
            <input type="submit" value="プロフィールを編集する">
          </form>
        </h3>
        <h3>
          <form action="login.php" method="post">
            <input type="submit" value="ログアウト">
          </form>
        </h3>
      </section>
    </div>
  </header>
  <main>
    <?php
    if (!empty($_POST["tweetUp"])) {

      $alreadyTweets = $pdo->query('SELECT users.first_name,users.last_name,tweets.id,tweets.tweet,tweets.user_id
      FROM (SELECT MAX(tweets.id) AS maxId
            FROM twitter.tweets
            )v,twitter.tweets
            LEFT JOIN twitter.users ON tweets.user_id = users.id
      WHERE tweets.id = maxId');

      $sameCount = 0;


      while ($alreadyTweet = $alreadyTweets->fetch()) {
        if ($_SESSION['id'] === $alreadyTweet['user_id']) {
          if (!empty($_POST["tweetUp"])) {

            if ($_POST["tweetUp"] === $alreadyTweet['tweet']) {
              $sameCount = 1;
            }
          }
        }
      }


      if ($sameCount === 0) {
        //インサート文を変数に格納する(変数は''で書く。””はエラーが発生する)。
        $insert = 'INSERT INTO tweets (user_id, tweet) VALUES (:user_id,:tweet)';
        //値が空のままのSQL文をprepare()にセットし、SQL実行のための準備を行う
        $upTweet = $pdo->prepare($insert);
        //実際に挿入する値を配列に格納
        $params = array(':user_id' => $_SESSION['id'], ':tweet' => $_POST["tweetUp"]);

        //そしてexecute()に値が入った配列をセットしてSQLを実行し、データベースにデータを挿入する
        $upTweet->execute($params);
        echo '<h2>投稿しました！</h2>';
      }

      $_POST = [];
    } ?>
    <section id="bulletinBoards">

      <h1>My Posts</h1>
      <ul class="myBoard">

        <?php
        $nonWrite = 1;
        $users = $pdo->query('SELECT * FROM twitter.users');
        $tweets = $pdo->query('SELECT * FROM twitter.tweets ORDER BY id DESC');
        while ($user = $users->fetch()) {
          if ($_SESSION['id'] === $user['id']) {
            while ($tweet = $tweets->fetch()) {
              if ($tweet['user_id'] === $user['id']) {

                $firstName = htmlentities($user['first_name']);
                $lastName = htmlentities($user['last_name']);
                $thisTweet = htmlentities($tweet['tweet']);
                echo '<li class="board">
                <form action="comment.php" method="post">
            <input type="hidden" name="tweetId" value="' . $tweet['id']  . '">
            <input type="submit" value="リプライを見に行く">
          </form><dl><dt>名前：' . $firstName . '　' . $lastName . '</dt><dd><p>' . $thisTweet . '</p></dd></dl></li>';
                $nonWrite = 0;
                $firstName = [];
                $lastName = [];
                $thisTweet = [];
              }
            }
          }
        }

        if ($nonWrite === 1) {
          echo '<li class="cover"><h1>新しい記事を投稿してみよう！</h1></li>';
        }
        ?>

      </ul>
      <h1>Everyone's posts</h1>
      <ul class="ourBoards">
        <?php
        $userTweets = $pdo->query('SELECT users.first_name,users.last_name,tweets.tweet,tweets.user_id,tweets.id
                                   FROM twitter.users
                                   INNER JOIN twitter.tweets
                                   ON users.id = tweets.user_id
                                   ORDER BY tweets.id DESC');
        while ($userTweet = $userTweets->fetch()) {
          if ($_SESSION['id'] !== $userTweet['user_id']) {
            $firstName = htmlentities($userTweet['first_name']);
            $lastName = htmlentities($userTweet['last_name']);
            $thisTweet = htmlentities($userTweet['tweet']);
            echo '
          <li class="board"><dl><dt>名前：' . $firstName . '　' . $lastName . '</dt><dd><p>' . $thisTweet . '</p></dd></dl>' .
              '<form action="comment.php" method="post">
            <input type="hidden" name="tweetId" value="' . $userTweet['id'] . '">
            <input type="submit" value="リプライを見に行く">
          </form></li>';
            $firstName = [];
            $lastName = [];
            $thisTweet = [];
          }
        }
        ?>

      </ul>
    </section>

    <form class="writting" action="article.php" method="post">
      <h2>ポストを投稿する</h2>
      <textarea name="tweetUp" id="" rows="10"></textarea>
      <input type="submit" value="投稿する">
    </form>
  </main>
  <footer></footer>

</body>

</html>