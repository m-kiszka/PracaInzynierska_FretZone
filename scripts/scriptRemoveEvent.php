<?php
  session_start(); require_once('./scriptConnect.php'); require_once('./scriptCheck_LoggedIn_Script.php');

  $event_id = $_SESSION["event_id"];
  unset($_SESSION["event_id"]);

  $removeOK=true;
  if(!isset($_SESSION["event_change"]))
  {
    echo "<script>window.location='../index.php'</script>;";
    die();
  }
  else
  {
    unset($_SESSION["event_change"]);
  }

  if(!$connect->query("UPDATE fz_chall SET status='LOCKED' WHERE id='$event_id'"))
  {
    $removeOK=false;
  }

  if($removeOK)
  {
    $_SESSION["event_error"]="Usunięto wydarzenie.";
    echo "<script>window.location='../events-list.php'</script>;";
    die();
  }
  else
  {
    $_SESSION["event_error"]="Wystąpił błąd przy usuwaniu wydarzenia.";
    echo "<script>window.location='../event.php?v=".$event_id."'</script>;";
    die();
  }
?>
