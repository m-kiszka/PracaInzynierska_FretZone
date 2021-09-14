<?php session_start(); require_once('./scripts/scriptConnect.php');

if(isset($_SESSION["verify_code"]))
{
  unset($_SESSION["verify_code"]);
}

if(isset($_SESSION["userid"]))
{
  ?> <script type="text/javascript">location.href = './index.php';</script> <?php
  die();
}
if(!isset($_GET["v"]))
{
  ?> <script type="text/javascript">location.href = './index.php';</script> <?php
  die();
}
else
{
  $verify_code=$_GET["v"];
  $verify_code=htmlspecialchars($verify_code);
  if($connect->query("UPDATE fz_users SET user_rank=1, verify_code=NULL WHERE verify_code='$verify_code'"))
  {
    $_SESSION["login_error"]="Konto zweryfikowane. Od tej pory można się na nie zalogować.";
    ?> <script type="text/javascript">location.href = './login.php';</script> <?php
    die();
  }
  else
  {
    $_SESSION["login_error"]="Wystąpił błąd przy weryfikacji konta.";
    ?> <script type="text/javascript">location.href = './index.php';</script> <?php
    die();
  }
}
?>
