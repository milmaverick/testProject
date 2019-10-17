<?php

require '../bd/bd.php';

define("LIMIT", 5);

function getPage (){
	// $_SESSION['page'] =	$_POST['page'] ?  $_POST['page'] : $_SESSION['page'];
	// if(isset($_POST['page'])){
	// 		$_SESSION['page']=$_POST['page'];
	// }
	// elseif (!isset($_SESSION['page'])) {
	// 	// code...
	// 		$_SESSION['page']=1;
	// }
	//
	// echo $_SESSION['page'];
	// return $_SESSION['page'];
	if(isset($_POST['page']))
	{
	 		return $_SESSION['page']= $_POST['page'];
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
	  	case 'update':
				action_update();
				break;
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
			case 'change':
	     	   action_change();
	     	   break;
			case 'access':
		 	     action_access();
		 	     break;
	}
}

function action_index(){
	$page = $_SESSION['page'];
	$tpg=($page-1)* LIMIT ;
  $pdo = DB::getInstance()->get_pdo();
	if(isset($_SESSION['logged_user'])){
			$query = 'SELECT * FROM `comments` ORDER BY date DESC LIMIT '.$tpg.','.LIMIT;
		  $comments = $pdo->query($query)->fetchAll();
			foreach ($comments as $comment) {
			?>
			<div class='comment <?if ($comment['isPass']==0) echo "deleteElement";?>'  id="comment<?=$comment['id']?>">
				<div class="media">
				  <img src='img/<?=$comment['img']?>' class="mr-3" alt='<?=$comment['name']?>'>
				  <div class="media-body">
						ИМЯ: <?=$comment['name']?>  |  email: <?=$comment['email']?>
						<span class='res isChanged <?if ($comment['isChanged']==0) echo "displayNone";?>'> Изменен Админом</span>

						<span class='res resChange'  data-toggle="modal" data-target="#exampleModal" id='change<?=$comment['id']?>'>
								<a href='#' onclick='changeElement(<?=$comment['id']?>)'> Изменить</a>
						</span>

							<span class='res resDel <?if ($comment['isPass']==0) echo "displayNone";?>'  id='delete<?=$comment['id']?>'>
								<a href='#' onclick='deleteElement(<?=$comment['id']?>)'> Отклонить</a>
							</span>

						<span class='res resReturn <?if($comment['isPass']==1) echo "displayNone";?>'  id='return<?=$comment['id']?>'>
									<a href='#' onclick='accessElement(<?=$comment['id']?>)'> Пропустить</a>
								</span>

					 <br> TEXT: <?=$comment['text']?>
				 </div>
				</div>
			</div>
	  <?	}
		}

	else {
			$query = 'SELECT * FROM `comments` WHERE `isPass`=1 ORDER BY date DESC LIMIT '.$tpg.','.LIMIT;
		  $comments = $pdo->query($query)->fetchAll();
			foreach ($comments as $comment) {
			?>
			<div class="comment">
				<div class="media">
				  <div class="media-body">
						ИМЯ: <?=$comment['name']?>  |  email: <?=$comment['email']?>
			    <br> TEXT:<?=$comment['text']?>
				 </div>
				</div>
			</div>
	  <?	}
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

function action_access()
{
	try {
		$pdo = DB::getInstance()->get_pdo();
		$id = htmlspecialchars($_POST['id']);
		$query = "UPDATE `comments` SET `isPass` = '1' WHERE `comments`.`id` = ".$id;
		$pdo->exec($query);
		echo "true";
	}
	catch (PDOException $e) {
		echo $e;
	}
}

function action_change()
{
	$pdo = DB::getInstance()->get_pdo();
	$id = htmlspecialchars($_POST['id']);
	$query = 'SELECT * FROM `comments` WHERE `comments`.`id` = '.$_POST['id'].' limit 1 ';
	$sth = $pdo->prepare($query);
	$sth->execute();
	$result = $sth->fetchAll();

	if($result){
		echo json_encode($result);
	}

}

function action_update()
{
	if($_POST['params']['id']){
		try {
					$id = htmlspecialchars($_POST['params']['id']);
					$name = htmlspecialchars($_POST['params']['name']);
					$email = htmlspecialchars($_POST['params']['email']);
					$text = htmlspecialchars($_POST['params']['text']);
					$query = "UPDATE `comments` SET `name` = '".$name."', `email` = '".$email."', `text` = '".$text."',
					`isPass` = '1', `isChanged`= '1' WHERE `comments`.`id` = ".$_POST['params']['id'];
					$pdo = DB::getInstance()->get_pdo();
					$pdo->exec($query);
					echo "true";
			}
			catch (PDOException $e) {
				echo $e;
			}
	}
	else{
		echo 'неверный id';
	}

}

function action_delete(){
	try {
		$pdo = DB::getInstance()->get_pdo();
		$id = htmlspecialchars($_POST['id']);
		$query = "UPDATE `comments` SET `isPass` = '0' WHERE `comments`.`id` = ".$id;
		$pdo->exec($query);
		echo "true";
	}
	catch (PDOException $e) {
		echo $e;
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
				echo "true";
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
