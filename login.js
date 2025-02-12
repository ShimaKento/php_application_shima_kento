document.addEventListener('DOMContentLoaded',function(){
  console.log('yahho');

  //ログインページ
  const sets = document.getElementById('login').children[0].children;

  const passview = sets[1].children[1];
  console.log(passview);
  console.log(sets[3]);

  const eys = sets[3].children[0].children;
  console.log (eys);

  const openeye = eys[0];
  const closeeye = eys[1];

  openeye.style.display = 'none';



  sets[3].addEventListener('mousedown',function(){
    passview.type = "text";
    closeeye.style.display = 'none';
    openeye.style.display = 'block';
  });
  sets[3].addEventListener('mouseup',function(){
    passview.type = "password";
    openeye.style.display = 'none';
    closeeye.style.display = 'block';
  });




});