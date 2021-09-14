<?php
session_start(); require_once('./scriptConnect.php'); require_once('./scriptCheck_LoggedIn_Script.php');
//require_once('./scriptFtpData.php');

$user_id=$_SESSION["userid"];

if(isset($_POST["ep_oldPassword"]))
{
  $old_password=$_POST["ep_oldPassword"];
  $old_password=process_data($old_password);
}
$user_id=$_SESSION["userid"];

$removeOK=true;
$passOK=true;

$result = $connect->query("SELECT password FROM fz_users WHERE id='$user_id'");
$result = $result->fetch_row();

if($result[0]==NULL || $result[0]=="")
{
  $passOK=true;
}
else
{
  if(!password_verify($old_password, $result[0]))
  {
    $removeOK=false;
    $passOK=false;
    $_SESSION["reset_error"] = "Podane hasło jest nieprawidłowe.";
  }
}

if($passOK)
{
  //if(!$connect->query("DELETE FROM fz_users WHERE id='$user_id'"))
  if(!$connect->query("UPDATE fz_users SET email=NULL, password=NULL, avatar_url=NULL, name_surname=NULL, localization=NULL, birth_date=NULL, user_rank='-1', verify_code=NULL WHERE id=$user_id"))
  {
    $removeOK=false;
  }
  else
  {
    if (file_exists("../uploads/avatars/avatar_".$_SESSION["login"].".png"))
    {
      if(!unlink("../uploads/avatars/avatar_".$_SESSION["login"].".png"))
      {
        $removeOK=false;
      }
    }

    //usuwanie wideo
    /*$videos=glob("../uploads/videos/".$_SESSION["login"]."_*.mp4");

    foreach($videos as $vid)
    {
      if(!unlink($vid))
      {
        $removeOK=false;
      }
    }*/
    //lokalnie
  }
}
if($removeOK)
{
  $_SESSION["login_error"] = "Konto zostało zamknięte pomyślnie.";
  echo "<script>window.location='./scriptLogout.php'</script>;";
  die();
}
else
{
  echo "<script>window.location='../edit-account.php'</script>;";
  die();
}

function process_data($dane)
{
  $dane = trim($dane); //usuwa biale znaki
  $dane = stripslashes($dane); //usuwa cudzyslow
  $dane = htmlspecialchars($dane); //zamienia poszczegolne znaki na odwolania znakowe, co uniemozliwia wywolanie kodu html w danym polu
  return $dane;
}
?>
