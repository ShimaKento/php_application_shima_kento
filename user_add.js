document.addEventListener('DOMContentLoaded', function () {
  //ユーザー追加ページ

  const userAdds = document.getElementById('userAdd').children[0].children;
  const email = userAdds[0].children[1];
  const password1 = userAdds[1].children[1];
  const password2 = userAdds[2].children[1];
  const firstName = userAdds[3].children[1];
  const lastName = userAdds[4].children[1];
  const send = userAdds[5].children[0];
  const viewBtn = userAdds[6];

  console.log(password1);

  console.log(password2);

  console.log(viewBtn);
  const eys = viewBtn.children[0].children;
  console.log (eys);

  const openeye = eys[0];
  const closeeye = eys[1];

  openeye.style.display = 'none';

  viewBtn.addEventListener('mousedown', function () {
    password1.type = "text";
    password2.type = "text";
    closeeye.style.display = 'none';
    openeye.style.display = 'block';
  });

  viewBtn.addEventListener('mouseup', function () {
    password1.type = "password";
    password2.type = "password";
    openeye.style.display = 'none';
    closeeye.style.display = 'block';
  });


  const emailET = userAdds[0].children[2];
  const passwordET = userAdds[1].children[2];
  const firstNameET = userAdds[3].children[2];
  const lastNameET = userAdds[4].children[2];
  send.addEventListener('click', function () {

    if (email.value === "") {
      emailET.textContent = 'メールアドレスを入力してください';
    } else {
      emailET.textContent = '';
    }

    if (password1.value === "" || password2.value === "") {
      passwordET.textContent = '２つの入力欄にパスワードを入力してください';
    } else {
      if (password1.value !== password2.value) {
        passwordET.textContent = '２つの入力欄のパスワードを同じにしてください';
      } else {
        emailET.textContent = '';
      }
    }

    if (firstName.value === "") {
      firstNameET.textContent = '名字を入力してください';
    } else {
      firstNameET.textContent = '';
    }

    if (lastName.value === "") {
      lastNameET.textContent = '氏名を入力してください';
    } else {
      lastNameET.textContent = '';
    }

  })

  const sendCommand = function () {
    if (email.value !== "" && password1.value !== "" && password2.value !== "" && firstName.value !== "" && lastName.value !== "" && password1.value === password2.value) {
      console.log("submit");
      send.type = 'submit';
    } else {
      console.log("button");
      send.type = 'button';
    }
  }

  email.addEventListener('input', function () {
    sendCommand();
  });

  password1.addEventListener('input', function () {
    sendCommand();
  });

  password2.addEventListener('input', function () {
    sendCommand();
  });

  firstName.addEventListener('input', function () {
    sendCommand();
  });

  lastName.addEventListener('input', function () {
    sendCommand();
  });



});