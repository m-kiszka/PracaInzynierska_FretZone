<?php
session_start(); require_once('./scriptConnect.php'); require_once('./scriptCheck_LoggedIn_Script.php');

if(isset($_POST["ep_oldPassword"]) && isset($_POST["ep_mail"]))
{
  $registerOK=true;
}
else
{
  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
}

$old_password=$_POST["ep_oldPassword"];
$old_password=process_data($old_password);
$user_id=$_SESSION["userid"];
$email=$_POST["ep_mail"];
$email=htmlspecialchars($email);

$result = $connect->query("SELECT password FROM fz_users WHERE id='$user_id'");
$result = $result->fetch_row();
if(!password_verify($old_password, $result[0]))
{
  $registerOK=false;
  $_SESSION["reset_error"] = "Podane hasło jest nieprawidłowe.";
}
else
{
  $old_password=password_hash($old_password, PASSWORD_DEFAULT);
}

if(strlen($email)>320)
{
  $registerOK=false;
  $_SESSION["register_error"] = "Podany adres e-mail jest zbyt długi.";
}

if($registerOK)
{
  $email = process_data($email);
  $connect->query("UPDATE fz_users SET email='$email' WHERE id='$user_id'");
  $_SESSION["reset_error"]="Adres e-mail został zmieniony.";
  ?> <script type="text/javascript">location.href = '../edit-account.php';</script> <?php
  die();
}
else
{
  ?> <script type="text/javascript">location.href = '../edit-account.php';</script> <?php
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
