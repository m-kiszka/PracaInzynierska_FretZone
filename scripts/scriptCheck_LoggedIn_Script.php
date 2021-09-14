<?php //sprawdzanie czy użytkownik jest zalogowany, a konto nie jest zawieszone
if(!isset($_SESSION["userid"]))
{
  $_SESSION["login_error"]="Musisz się zalogować, aby skorzystać z tej funkcji.";
  ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
  die();
}
else
{
  $temp_session=$_SESSION["userid"];
  $temp = $connect->query("SELECT user_rank FROM fz_users WHERE id='$temp_session'");
  $temp = $temp->fetch_row();
  if($temp[0]==-1)
  {
    ?> <script type="text/javascript">location.href = './scripts/scriptLogout.php';</script> <?php
    die();
  }
}
?>
