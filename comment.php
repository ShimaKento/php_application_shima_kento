<!doctype html>
<html lang="ja">

<head>
  <link rel="stylesheet" href="style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title>コメント欄</title>
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
  //GETで個々のページに飛び、付いているコメントを表示する。また、コメント追加でreplysに追加クエリを作りつつ、ページ更新で表示する。
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
  </header>
  <main>
    <?php if (!empty($_POST["commentUp"])) {

      $alreadyReplys = $pdo->query('SELECT replys.reply,replys.tweet_id,replys.user_id
      FROM twitter.replys');

      $sameCount = 0;

      while ($alreadyReply = $alreadyReplys->fetch()) {
        if (intval($_POST["tweetId"]) === $alreadyReply['tweet_id'] && $_SESSION['id'] === $alreadyReply['user_id']) {
          if ($_POST["commentUp"] === $alreadyReply['reply']) {
            $sameCount = 1;
          }
        }
      }

      if ($sameCount === 0) {

        //インサート文を変数に格納する(変数は''で書く。””はエラーが発生する)。
        $insert = 'INSERT INTO replys (tweet_id,user_id, reply) VALUES (:tweet_id,:user_id,:reply)';
        //値が空のままのSQL文をprepare()にセットし、SQL実行のための準備を行う
        $upReply = $pdo->prepare($insert);
        //実際に挿入する値を配列に格納
        $params = array(':tweet_id' => intval($_POST["tweetId"]), ':user_id' => $_SESSION['id'], ':reply' => $_POST["commentUp"]);

        //そしてexecute()に値が入った配列をセットしてSQLを実行し、データベースにデータを挿入する
        $upReply->execute($params);
        echo '<h2>返信を投稿しました！</h2>';
      }
    }

    $_POST["commentUp"] = [];
    ?>
    <section id="parenteBoard" class="parenteBoard">
      <div class="cover">
      <?php
      $userTweets = $pdo->query('SELECT tweets.id,users.first_name,users.last_name,tweets.tweet,tweets.user_id
                                 FROM twitter.tweets
                                 INNER JOIN twitter.users
                                 ON users.id = tweets.user_id');


      while ($userTweet = $userTweets->fetch()) {
        if (intval($_POST["tweetId"]) === $userTweet['id']) {
            $firstName = htmlentities($userTweet['first_name']);
            $lastName = htmlentities($userTweet['last_name']);
            $thisTweet = htmlentities($userTweet['tweet']);
            echo  "<p><span>" . $firstName ." ". $lastName . "　</span>" . $thisTweet . "</p>";
            $firstName = [];
            $lastName = [];
            $thisTweet = [];
        }
      }



      //理屈：カラムを３つつなげ、replyのtweet_idがtweetのidと一致するコメントを取得、replyのid順に表示
      echo '<section class="replys">';

      //この投稿へのリプライを取得
      $replys = $pdo->query('SELECT users.first_name,users.last_name,replys.reply,replys.tweet_id,replys.user_id
                             FROM twitter.replys
                             LEFT JOIN twitter.tweets
                             ON replys.tweet_id = tweets.id
                             LEFT JOIN twitter.users
                             ON replys.user_id = users.id
                             ORDER BY replys.id DESC');
      while ($reply = $replys->fetch()) {
        if (intval($_POST["tweetId"]) === $reply['tweet_id']) {
          $firstName = htmlentities($reply['first_name']);
          $lastName = htmlentities($reply['last_name']);
          $thisReply = htmlentities($reply['reply']);
          echo '<div class="repBox"><div class="screw T L"><div class="cover"><span></span><span></span></div></div>
        <div class="screw T R"><div class="cover"><span></span><span></span></div></div>
        <div class="screw B L"><div class="cover"><span></span><span></span></div></div>
        <div class="screw B R"><div class="cover"><span></span><span></span></div></div>
        <div class="name">' . $firstName . '　' . $lastName . '</div><p class="reply">' . $thisReply . '</p></div>';
        $firstName = [];
        $lastName = [];
        $thisReply = [];
        }
      }
      //リプライ欄を表示 
      $replyUsers = $pdo->query('SELECT replys.reply,replys.user_id,users.id,first_name,last_name
                             FROM twitter.replys
                             LEFT JOIN twitter.users ON replys.user_id = users.id');

      echo '</section>';
      ?>

        <div class="screw T L"><div class="cover"><span></span><span></span></div></div>
        <div class="screw T R"><div class="cover"><span></span><span></span></div></div>
        <div class="screw B L"><div class="cover"><span></span><span></span></div></div>
        <div class="screw B R"><div class="cover"><span></span><span></span></div></div>
      </div>
    </section>
    <form class="newReply" action="comment.php" method="post">
      <div class="cover">
        <h2>投稿主にリプライをする</h2>
        <input type="hidden" name="tweetId" value="<?php echo intval($_POST["tweetId"]) ?>">
        <textarea name="commentUp" rows="5"></textarea>
        <input type="submit" value="投稿する">
      </div>
    </form>
    <form class="back" action="article.php" method="post">
      <input type="submit" value="全体の掲示板に戻る">
    </form>


  </main>
  <footer></footer>
  <script src="comment.js" defer></script>
</body>

</html>