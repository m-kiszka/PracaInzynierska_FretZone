<?php
  session_start(); require_once('./scriptConnect.php'); require_once('./scriptCheck_LoggedIn_Script.php');

  $tab_id = $_SESSION["tab_id"];
  unset($_SESSION["tab_id"]);

  $removeOK=true;
  if(!isset($_SESSION["tab_change"]))
  {
    echo "<script>window.location='../index.php'</script>;";
    die();
  }
  else
  {
    unset($_SESSION["tab_change"]);
  }

  if(!$connect->query("UPDATE fz_tabs SET status='LOCKED' WHERE id='$tab_id'"))
  {
    $removeOK=false;
  }

  if($removeOK)
  {
    $_SESSION["tabs_error"]="Usunięto tabulaturę.";
    echo "<script>window.location='../tabs-list.php'</script>;";
    die();
  }
  else
  {
    $_SESSION["tabs_error"]="Wystąpił błąd przy usuwaniu tabulatury.";
    echo "<script>window.location='../tab.php?v=".$tab_id."'</script>;";
    die();
  }
?>
