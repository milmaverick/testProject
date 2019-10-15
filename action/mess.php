<?php

require '../bd/bd.php';

define("LIMIT", 5);

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

function action_index(){
	$page = getPage();
	$tpg=($page-1)* LIMIT ;
  $pdo = DB::getInstance()->get_pdo();
	if(isset($_SESSION['logged_user'])){
			$query = 'SELECT * FROM `comments` ORDER BY date DESC LIMIT '.$tpg.','.LIMIT;
		  $comments = $pdo->query($query)->fetchAll();
			foreach ($comments as $comment) {
	  		echo "<div class='comment'> ИМЯ: ". $comment['name']." |  email:" . $comment['email'] . "<span class='res'  id='comment".$comment['id']."'><a href='#'
				onclick='deleteElement(".$comment['id'].")'> Удалить</a></span>".
	  		"<br> TEXT:".$comment['text']."</div>";
	  	}
		}

	else {
			// code...
			$query = 'SELECT * FROM `comments` WHERE `isPass`=1 ORDER BY date DESC LIMIT '.$tpg.','.LIMIT;
		  $comments = $pdo->query($query)->fetchAll();
			foreach ($comments as $comment) {
	  		echo "<div class='comment'> ИМЯ: ". $comment['name']." |  email: " . $comment['email'] .
	  		"<br> TEXT:".$comment['text']."</div>";
	  	}
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

		if(empty($errors))
		{
			try {
				$pdo = DB::getInstance()->get_pdo();
				$name = htmlspecialchars($_POST['params']['name']);
				$email = htmlspecialchars($_POST['params']['email']);
				$text = htmlspecialchars($_POST['params']['text']);
				// $query = "INSERT INTO `comments` (`id`, `name`, `email`, `text`, `date`, `isPass`) VALUES (NULL, '{$name}', '{$email}',
				// 	'{$text}', CURRENT_TIMESTAMP, '0')";
				$safe = $pdo->prepare("INSERT INTO `comments` SET name= :name, email= :email, text= :text, date=CURRENT_TIMESTAMP , isPass='0' ");
				$arr= ['name'=> $name, 'email'=> $email, 'text'=> $text];
				$safe->exec($arr);
			}
			catch (PDOException $e) {
				echo "Ошибка связи с бд";
			}
		}
		else{
			echo array_shift($errors);
		}
}

function pagination(){
	$page = getPage();

	if (isset($_SESSION['logged_user'])) {
		// code...
		$query = 'SELECT count(id) as count FROM `comments` ';
	}
	else{
		$query = 'SELECT count(id) as count FROM `comments` WHERE `isPass`=1';
	}
  $pdo = DB::getInstance()->get_pdo();
  $pages = $pdo->query($query)->fetchAll();

	$numb= ceil($pages[0]['count']/LIMIT);
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
	try {
		$pdo = DB::getInstance()->get_pdo();
		$id = htmlspecialchars($_POST['id']);
		$query = "UPDATE `comments` SET `isPass` = '0' WHERE `comments`.`id` = ".$id;
		$pdo->exec($query);
	}
	catch (PDOException $e) {
		echo "Ошибка связи с бд";
	}
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

function action_isLogged(){
  if(isset($_SESSION['logged_user']))
  {
    echo "true";
		return true;
  }
  else {
    echo "false";
		return false;
  }
}

function action_logOut(){
  if(action_isLogged())
  {
    unset($_SESSION['logged_user']);
  	echo "Вышел из Аккаунт Php";
  }
  else {
    echo "Нету акка1";
  }
}



 ?>
