<?php
require_once('./scriptConnect.php');
if(isset($_REQUEST["requested"]))
{
  $requestedLogin = $_REQUEST["requested"];
  $resultLogin = $connect->query("SELECT * FROM fz_users WHERE login = '$requestedLogin'");
  $resultLogin = $resultLogin->fetch_row();
  if($resultLogin!=null)
  {
    echo "Podany login jest niedostępny.";
  }
  else
  {
    echo "Podany login jest dostępny!";
  }
}
?>
