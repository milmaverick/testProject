<?php
require '../bd/bd.php';

if ( isset($_POST['action'])){
  switch ( $_POST['action'] )
  {
      case 'isLogged':
          action_isLogged();
          break;
      case 'logOut':
          action_logOut();
          break;
      case 'delete':
          action_delete();
          break;
      case 'signin':
         action_signin();
         break;
      case 'update':
           action_update();
           break;
      case 'access':
           action_access();
           break;
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

 ?>
