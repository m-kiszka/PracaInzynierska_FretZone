<?php
session_start();
require_once('./scriptConnect.php');

if(!isset($_POST["forgot_password"]))
{
  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
}
else
{
  $email = $_POST["forgot_password"];
  $result = $connect->query("SELECT user_rank, password FROM fz_users WHERE email='$email'");
  $result = $result->fetch_row();
  if(isset($result) && $result[0]!="" && $result[0]>0 && $result[1]!="")
  {
    $verify_code = md5($email.rand(0,1000));

    $mail_headers = "From: welcome@fretzone.pl\r\n";
    $mail_headers .= "MIME-Version: 1.0\r\n";
    $mail_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $mail_headers .= "X-Priority: 1\r\n";

    //$mail_msg = "Wejdź na podany adres, aby zresetować swoje hasło: \n\nhttp://fretzone.westeurope.cloudapp.azure.com/reset-password.php?v=".$verify_code;
    $mail_msg = "Wejdź na podany adres, aby zresetować swoje hasło: \n\nhttps://www.fretzone.pl/reset-password.php?v=".$verify_code;

    $connect->query("UPDATE fz_users SET verify_code='$verify_code' WHERE email='$email'");

    mail($email, "Zresetuj hasło - FretZone", $mail_msg, $mail_headers);

    $_SESSION["forgot_error"]="Wysłano wiadomość z linkiem do resetowania hasła na podany adres e-mail.";

    ?> <script type="text/javascript">location.href = '../forgot-password.php';</script> <?php
    die();
  }
  else
  {
    $_SESSION["forgot_error"]="Wystąpił błąd.";
    ?> <script type="text/javascript">location.href = '../forgot-password.php';</script> <?php
    die();
  }
}
?>
