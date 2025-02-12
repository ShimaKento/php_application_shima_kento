document.addEventListener('DOMContentLoaded', function () {
  console.log('yahho');

  //ログインページ
  const parents = document.getElementById('parenteBoard');

  console.log(parents);

  const replys = document.getElementsByClassName("replys");

  console.log(replys);

  const reply = replys[0].children;

  console.log(reply);

  const replyTexts = [];

  for (i = 0; i < reply.length; i++) {
    replyTexts[i] = reply[i].getElementsByClassName("reply");
    console.log(replyTexts[i][0].clientHeight);

    reply[i].style.height = 100 - 54 + (replyTexts[i][0].clientHeight + 10) + "px";
    console.log(reply[i].clientHeight);
  }


});