document.addEventListener('DOMContentLoaded',function(){
  //ユーザー編集ページ

  const userCustoms = document.getElementById('userCustom').children;
  console.log(userCustoms);

  const oldPassword = userCustoms[10];
  const newPassword = userCustoms[11];
  const againPassword = userCustoms[12];
  const passwordBtn = userCustoms[13];
  console.log(passwordBtn);

  const eys = passwordBtn.children[0].children;
  console.log (eys);

  const openeye = eys[0];
  const closeeye = eys[1];

  openeye.style.display = 'none';

  passwordBtn.addEventListener('mousedown', function () {
    oldPassword.type = "text";
    newPassword.type = "text";
    againPassword.type = "text";
    closeeye.style.display = 'none';
    openeye.style.display = 'block';
  });

  passwordBtn.addEventListener('mouseup', function () {
    oldPassword.type = "password";
    newPassword.type = "password";
    againPassword.type = "password";
    openeye.style.display = 'none';
    closeeye.style.display = 'block';
  });

  const changeBtn = userCustoms[17];

  /*  oldPassword.addEventListener('input',function(){
    if ( oldPassword.value !== "") {
      console.log("submit");
      changeBtn.type = 'submit';
    } else {
      changeBtn.type = 'button';
    }
  });

  changeBtn.addEventListener('click',function(){
      if(oldPassword.value === ""){
        nowPassword.style.color = 'red';
        nowPassword.style.fontSize = '18px';
        console.log("red!!");
      }
  }); */


});