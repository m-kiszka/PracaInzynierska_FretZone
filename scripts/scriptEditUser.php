<?php
  session_start();
  require_once('./scriptConnect.php');
  //require_once('./scriptFtpData.php');
  require_once('./scriptCheck_Moderator.php');

  if(!isset($_SESSION["edit_user_login"]))
  {
    $_SESSION["edit_user_error"] = "Wystąpił błąd.";
    ?> <script type="text/javascript">location.href = '../users-list.php';</script> <?php
    die();
  }

  $login = $_SESSION["edit_user_login"];
  unset($_SESSION["edit_user_login"]);
  $rank = $_POST["user_rank"];

  if($connect->query("UPDATE fz_users SET user_rank='$rank' WHERE login='$login'"))
  {
    if($rank==-1)
    {
      $connect->query("UPDATE fz_users SET password=NULL, name_surname=NULL, localization=NULL, birth_date=NULL, verify_code=NULL, avatar_url=NULL WHERE login='$login'");

      if (file_exists("../uploads/avatars/avatar_".$login.".png"))
      {
        if(!unlink("../uploads/avatars/avatar_".$login.".png"))
        {
          $removeOK=false;
        }
      }

      /*
      $videos=glob("../uploads/videos/".$login."_*.mp4");

      foreach($videos as $vid)
      {
        if(!unlink($vid))
        {
          $removeOK=false;
        }
      }*/
    }
    $_SESSION["edit_user_error"] = "Zaktualizowano dane.";
    ?> <script type="text/javascript">location.href = '../edit-user.php?p=<?php echo $login ?>';</script> <?php
    die();
  }
  else
  {
    $_SESSION["edit_user_error"] = "Wystąpił błąd przy wysyłaniu danych.";
    ?> <script type="text/javascript">location.href = '../edit-user.php?p=<?php echo $login ?>';</script> <?php
    die();
  }
?>
