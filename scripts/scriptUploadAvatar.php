<?php
session_start();
require_once('./scriptConnect.php');
require_once('./scriptFtpData.php');
require_once('./scriptCheck_LoggedIn_Script.php');

$uploadOK = true;
$directory = "..\uploads\avatars\\";

$file = $_FILES["fileToUpload"]["tmp_name"];

if(!isset($_POST["submitPhoto"]) || $file==null) //sprawdzanie czy jest wysylany plik
{
  $_SESSION["upload-error"] = "Podany plik jest nieprawidłowy.";
  ?><script type="text/javascript">location.href = '../edit-profile.php';</script><?php
  die();
}

$fileDim = getimagesize($file);

if(!isset($_SESSION["userid"]))
{
  $uploadOK = false;
}
else
{
  $temp_session=$_SESSION["userid"];
  $temp = $connect->query("SELECT user_rank FROM fz_users WHERE id='$temp_session'");
  $temp = $temp->fetch_row();
  if($temp[0]==-1)
  {
    ?> <script type="text/javascript">location.href = './scriptLogout.php';</script> <?php
    die();
  }
}

//format pliku
$fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));

//sprawdzanie formatu pliku
if($fileType != "jpg" && $fileType != "jpeg" && $fileType != "png")
{
  $uploadOK = false;
  $_SESSION["upload-error"] = "Nieprawidłowy format pliku.";
}

if ($_FILES["fileToUpload"]["size"] > 500000) //sprawdzanie rozmiaru pliku, max 500KB
{
  $uploadOK = false;
  $_SESSION["upload-error"] = "Rozmiar pliku jest zbyt duży.";
}

//czy ostatecznie mozna wyslac plik na serwer
if (!$uploadOK)
{
  ?><script type="text/javascript">location.href = '../edit-profile.php';</script><?php
  die();
}
//ostateczny upload pliku
else
{
  $fileName = $_SESSION["login"].".png";

  $user_id = $_SESSION['userid'];

  //MOVE UPLOADED
  /*
  if(move_uploaded_file($file, $directory.$fileName) && $connect->query("UPDATE fz_users SET avatar_url='avatar_$fileName' WHERE id='$user_id'"))
  {
    switch($fileType)
    {
      case "jpg":
        $tempType = imagecreatefromjpeg($directory.$fileName);
      break;
      case "jpeg":
        $tempType = imagecreatefromjpeg($directory.$fileName);
      break;
      case "png":
        $tempType = imagecreatefrompng($directory.$fileName);
      break;
    }
    $resizedLayer = scaleAvatar($tempType,$fileDim[0],$fileDim[1]);
    imagepng($resizedLayer,$directory."avatar_".$fileName);
    unlink($directory.$fileName);

    $_SESSION["upload-error"] = "Plik został wysłany poprawnie.";
    ?><script type="text/javascript">location.href = '../edit-profile.php';</script><?php
  }
  else
  {
    $_SESSION["upload-error"] = "Wystąpił błąd przy przesyłaniu pliku.";
    ?><script type="text/javascript">location.href = '../edit-profile.php';</script><?php
  }
  */
  //MOVE UPLOADED

  //FTP
  if(ftp_put($ftp_conn, '//PracaInz_Final/uploads/avatars/'.$fileName ,$file, FTP_BINARY) && $connect->query("UPDATE fz_users SET avatar_url='avatar_$fileName' WHERE id='$user_id'"))
  {
    chdir('C:\xampp\htdocs\PracaInz_Final\uploads\avatars');
    switch($fileType)
    {
      case "jpg":
        $tempType = imagecreatefromjpeg($fileName);
      break;
      case "jpeg":
        $tempType = imagecreatefromjpeg($fileName);
      break;
      case "png":
        $tempType = imagecreatefrompng($fileName);
      break;
    }
    $resizedLayer = scaleAvatar($tempType,$fileDim[0],$fileDim[1]);
    imagepng($resizedLayer,"avatar_".$fileName);
    ftp_delete($ftp_conn, '//PracaInz_Final/uploads/avatars/'.$fileName);

    $_SESSION["upload-error"] = "Plik został wysłany poprawnie.";
    ?><script type="text/javascript">location.href = '../edit-profile.php';</script><?php
    die();
  }
  else
  {
    echo $file." ".$fileName;
    /*$_SESSION["upload-error"] = "Wystąpił błąd przy przesyłaniu pliku.";
    ?><script type="text/javascript">location.href = '../edit-profile.php';</script><?php*/
  }

  ftp_close($ftp_conn);
  //FTP
}

//funkcja od skalowania
function scaleAvatar($avatarType, $avatarWidth, $avatarHeight)
{
    $avatarLayer = imagecreatetruecolor(300, 300);
    imagecopyresampled($avatarLayer, $avatarType, 0, 0, 0, 0, 300, 300, $avatarWidth, $avatarHeight);
    return $avatarLayer;
}
?>
