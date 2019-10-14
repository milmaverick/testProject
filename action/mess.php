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
	// $comments= R::getAll('SELECT * FROM `comments` ORDER BY date DESC LIMIT '.$tpg.','.limit.' ');
	// foreach ($comments as $comment) {
	// 	echo "<div id='comment'> ИМЯ: ". $comment['name']." |  email:" . $comment['email'] . "<span id='res'><a href='#' onclick='deleteElement(".$comment['id'].")'> Удалить</a></span>
	// 	<br> TEXT:".$comment['text']."</div>";
	// }
  $pdo = DB::getInstance()->get_pdo();

  //  var_dump($comments);
  // var_dump($connect);
  $query = 'SELECT * FROM `comments` ORDER BY date DESC LIMIT '.$tpg.','.limit;
  $comments = $pdo->query($query)->fetchAll();
  // $comments = $connect->query("select * from `comments`");
  foreach ($comments as $comment) {
		echo "<div id='comment'> ИМЯ: ". $comment['name']." |  email:" . $comment['email'] . "<span id='res'><a href='#' onclick='deleteElement(".$comment['id'].")'> Удалить</a></span>
		<br> TEXT:".$comment['text']."</div>";
	}

}




function pagination(){
	$page = getPage();
	$pages= R::getAll('SELECT count(id) as count FROM `comments` ');
	$cls;
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

 ?>
