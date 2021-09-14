<?php
require_once('./scriptConnect.php');
if(isset($_REQUEST["requested"]))
{
  $requestedEmail = $_REQUEST["requested"];
  $resultEmail = $connect->query("SELECT * FROM fz_users WHERE email = '$requestedEmail'");
  $resultEmail = $resultEmail->fetch_row();
  if($resultEmail!=null)
  {
    echo "Podany adres e-mail jest niedostępny.";
  }
  else
  {
    echo "Podany adres e-mail jest dostępny!";
  }
}
?>
