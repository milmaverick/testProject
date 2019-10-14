<?php

require '../bd/bd.php';

define("limit", 5);
function getPage (){
	if(!isset($_POST['page']))
	{
		$page=1;
	}
	else
	{
		$page= $_POST['page'];
	}
	return $page;
}

if ( isset($_POST['action'])){
	switch ( $_POST['action'] )
	{
      case 'isLogged':
	        action_isLogged();
	        break;

	    case 'logOut':
	        action_logOut();
	        break;

	    case 'index':
	        action_index();
	        break;
	    case 'delete':
	        action_delete();
	        break;
	    case 'add':
	        action_add();
	        break;
	    case 'pagination':
	        pagination();
	        break;
      case 'signin':
    	   action_signin();
    	   break;
	}
}

function action_add(){
	$errors=array();
		if(trim($_POST['params']['name'])==''){
			{
				$errors[]='Введите имя';
			}
		}
		if(trim($_POST['params']['email'])==''){
			{
				$errors[]='Введите email';
			}
		}
		if($_POST['params']['text']==''){
			{
				$errors[]='Введите текст';
			}
		}

		if(!preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}
			[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', trim($_POST['params']['email'])))
		{
			$errors[]='Некорректный email';
		}
		if(empty($errors))
		{
			$comment= R::dispense('comments');
			$comment->name = $_POST['params']['name'];
			$comment->email = $_POST['params']['email'];
			$comment->text = $_POST['params']['text'];

			R::store($comment);
			echo "Комментарий добавлен";
		}
		else{
			echo array_shift($errors);
		}
}

function action_index(){
	$page = getPage();

	$tpg=($page-1)* limit ;

  $pdo = DB::getInstance()->get_pdo();
  $query = 'SELECT * FROM `comments` ORDER BY date DESC LIMIT '.$tpg.','.limit;
  $comments = $pdo->query($query)->fetchAll();
  if(isset($_SESSION['logged_user'])){
    foreach ($comments as $comment) {
      echo "<div id='comment'> ИМЯ: ". $comment['name']." |  email:" . $comment['email'] .
      "<span id='res'><a href='#' onclick='deleteElement(".$comment['id'].")'> Удалить</a></span>
  		<br> TEXT:".$comment['text']."</div>";
    }
  }
  else{
    foreach ($comments as $comment) {
  		echo "<div id='comment'> ИМЯ: ". $comment['name']." |  email:" . $comment['email'] .
  		"<br> TEXT:".$comment['text']."</div>";
  	}
  }
}

function pagination(){
	$page = getPage();
  $query = 'SELECT count(id) as count FROM `comments` ';
  $pdo = DB::getInstance()->get_pdo();
  $pages = $pdo->query($query)->fetchAll();

	$numb= ceil($pages[0]['count']/limit);
	for ($i=1; $i <= $numb; $i++)
	{
		 if($page==$i)
		 	{
		 		$cls="page-item active";
			}
			else $cls ="page-item";
			echo '<li class="'.$cls.'"><a class="page-link"
				  href="#"  onclick="pagination('.$i.')">'.$i.'</a></li>';
	}
}

function  action_delete(){
	$comment = R::load('comments',$_POST['id']);
	R::trash($comment);
	echo "Удалено ";
}


function action_signin(){
		$errors= array();
    $query = "SELECT * FROM `admin` WHERE `login`='".$_POST['params']['admin']."'  limit 1";
    $pdo = DB::getInstance()->get_pdo();
    $user = $pdo->query($query)->fetchAll();

		if($user)
		{
			if($_POST['params']['passwd']==$user[0]['psw'])
			{

				$_SESSION['logged_user']=$user[0]['login'];
				echo $_SESSION['logged_user'] . '  '. $user[0]['login'];
			}
			else{
				$errors[]='неверный пароль';
			}
		}
		else
		{
			$errors[]='Пользователь не найден';
		}

	if(!empty($errors)){
			echo array_shift($errors);
		}
}

function action_logOut(){
  if(isset($_SESSION['logged_user']))
  {
    unset($_SESSION['logged_user']);
  	echo "Вышел из Аккаунт Php";
  }
  else {
    echo "Нету акка1";
  }
}

 ?>
