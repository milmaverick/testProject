<?php

require '../bd/bd.php';

if ( isset($_POST))
{
  $errors=array();
		if(trim($_POST['name'])==''){
			{
				$errors[]='Введите имя';
			}
		}
		if(trim($_POST['email'])==''){
			{
				$errors[]='Введите email';
			}
		}
		if($_POST['text']==''){
			{
				$errors[]='Введите текст';
			}
		}

		if(empty($errors))
		{
  			try {
  				$pdo = DB::getInstance()->get_pdo();
  				$name = htmlspecialchars($_POST['name']);
  				$email = htmlspecialchars($_POST['email']);
  				$text = htmlspecialchars($_POST['text']);
          $safe = $pdo->prepare("INSERT INTO `comments` SET name= :name, email= :email,
            text= :text, date=CURRENT_TIMESTAMP , img= :img  ,isPass='0' ");
  				$arr= ['name'=> $name, 'email'=> $email, 'text'=> $text , 'img' => $_FILES['uploadimage']['name'] ? $_FILES['uploadimage']['name'] : "no-image.png"  ];
  				$safe->execute($arr);

          if ( isset($_FILES['uploadimage'])){
            list($width, $height) = getimagesize($_FILES['uploadimage']['tmp_name'] );
            if($width> 320 || $height> 240)
            {
              $newwidth = 320;
              $newheight = 240;
              $thumb = imagecreatetruecolor($newwidth, $newheight);

              switch($_FILES['uploadimage']['type']){
                  case 'image/jpeg': $source = imagecreatefromjpeg($_FILES['uploadimage']['tmp_name']); break; //Создаём изображения по
                  case 'image/png': $source = imagecreatefrompng($_FILES['uploadimage']['tmp_name']); break;  //образцу загруженного
                  case 'image/gif': $source = imagecreatefromgif($_FILES['uploadimage']['tmp_name']); break; //исходя из его формата
                  default: return false;
              }
              imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
              imagejpeg($thumb, "../img/".$_FILES['uploadimage']['name'],0, -1);
           }
           else {
             move_uploaded_file($_FILES['uploadimage']['tmp_name'], '../img/' . $_FILES['uploadimage']['name']);
           }

           }
  				echo "true";
  			}
  			catch (Exception  $e) {
  				echo $e;
  			}
		}
		else{
			echo array_shift($errors);
		}
}
 ?>
