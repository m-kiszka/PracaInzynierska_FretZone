<?php
session_start();
require_once('./scriptConnect.php');
//require_once('./scriptFtpData.php');
require_once('./scriptCheck_LoggedIn_Script.php');

if(isset($_SESSION["perf_id"]) && isset($_SESSION["perf_change"]))
{
  $id = $_SESSION["perf_id"];
  $name = $_POST["name"];
  $desc = $_POST["description"];
  unset($_SESSION["perf_change"]);
}
else
{
  ?> <script type="text/javascript">location.href = '../videos-list.php';</script> <?php
  die();
}

$uploadOK=true;

if(is_null($name) || $name=="")
{
  $_SESSION["upload_error"]="Wypełnij wszystkie obowiązkowe pola.";
  $uploadOK = false;
}
else
{
  if(strlen($name)<3 && strlen($name)>128)
  {
    $_SESSION["upload_error"] = "Nazwa musi zawierać więcej niż 3 znaki i mniej niż 128 znaków.";
    $uploadOK = false;
  }

  if (preg_match("/[^A-Za-z0-9ąĄęĘłŁóÓżŻśŚćĆńŃ_'.!?&()+: -]/", $name))
  {
    $_SESSION["upload_error"] = "Nazwa zawiera niedozwolone znaki.";
    $uploadOK=false;
  }

  if(!is_null($desc))
  {
    if(strlen($desc)>512)
    {
      $_SESSION["upload_error"] = "Opis nie może zawierać więcej niż 512 znaków.";
      $uploadOK = false;
    }
    if (preg_match("/[^A-Za-z0-9ąĄęĘłŁóÓżŻśŚćĆńŃ_'.!?&()+: -]/", $name))
    {
      $_SESSION["upload_error"] = "Opis zawiera niedozwolone znaki.";
      $uploadOK = false;
    }
  }
  else
  {
    $desc = "";
  }
}

//czy ostatecznie mozna wyslac plik na serwer
if (!$uploadOK)
{
  ?><script type="text/javascript">location.href = '../edit-video.php';</script><?php
  die();
}
//ostateczny upload pliku
else
{
  if($connect->query("UPDATE fz_videos SET name='$name', description='$desc' WHERE id='$id'"))
  {
    unset($_SESSION["perf_id"]);    
    echo "<script>location.href=\"../play.php?v=$id\";</script>";
    die();
  }
  else
  {
    $_SESSION["upload-error"] = "Wystąpił błąd przy wprowadzaniu zmian.";
    ?><script type="text/javascript">location.href = '../edit-video.php';</script><?php
    die();
  }
}
?>
