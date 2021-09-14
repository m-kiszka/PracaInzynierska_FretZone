<?php
session_start(); require_once('./scriptConnect.php'); require_once('./scriptCheck_LoggedIn_Script.php');

if(isset($_POST["ep_password"]) && isset($_POST["ep_repeatPassword"]))
{
  $registerOK=true;
}
else
{
  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
}

$password=$_POST["ep_password"];
$confirm_password=$_POST["ep_repeatPassword"];
$user_id=$_SESSION["userid"];

if($password!=$confirm_password)
{
  $registerOK=false;
  $_SESSION["reset_error"] = "Podane hasła nie są identyczne.";
}

//sprawdzanie czy haslo jest bezpieczne
if(!preg_match("#[0-9]+#", $password))
{
  $registerOK=false;
  $_SESSION["reset_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
}

if(!preg_match("#[a-z]+#", $password))
{
  $registerOK=false;
  $_SESSION["reset_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
}

if(!preg_match("#[A-Z]+#", $password))
{
  $registerOK=false;
  $_SESSION["reset_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
}

if(!preg_match("/[\'^£$%&*()}{@#~?><>,|=_+!-]/", $password))
{
  $registerOK=false;
  $_SESSION["reset_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
}

//sprawdzanie czy hasło mieści się w limicie znaków
if(strlen($password)<8 || strlen($password)>255)
{
  $registerOK=false;
  $_SESSION["reset_error"] = "Hasło nie może być krótsze niż 8 znaków.";
}

if($registerOK)
{
  $password = process_data($password);
  $password = password_hash($password, PASSWORD_DEFAULT);
  $connect->query("UPDATE fz_users SET password='$password' WHERE id='$user_id'");
  $_SESSION["reset_error"]="Hasło zostało ustawione.";
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
