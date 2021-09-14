<?php
session_start(); require_once('./scriptConnect.php');

if(isset($_POST["password_reset"]) && isset($_POST["confirm_reset"]))
{
  $registerOK=true;
}
else
{
  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
}



$password=$_POST["password_reset"];
$confirm_password=$_POST["confirm_reset"];
$verify_code=$_SESSION["verify_code"];

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
  $connect->query("UPDATE fz_users SET password='$password', verify_code=NULL WHERE verify_code='$verify_code'");
  $_SESSION["login_error"]="Hasło zostało zresetowane pomyślnie.";
  unset($_SESSION["verify_code"]);
  ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
  die();
}
else
{
  ?> <script type="text/javascript">location.href = '../reset-password.php?v=<?php echo $verify_code; ?>';</script> <?php
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
