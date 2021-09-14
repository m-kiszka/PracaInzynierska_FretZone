<?php
  session_start(); require_once('./scriptConnect.php'); require_once('./scriptCheck_Moderator.php');

  $report_id = $_GET["id"];

  $removeOK=true;

  $result=$connect->query("SELECT description FROM fz_reports WHERE id='$report_id'");
  $result = $result->fetch_row();

  $temp = $_SESSION["login"];

  $temp_text = $result[0]."\n\nZamknięte przez: ".$temp;

  if(!$connect->query("UPDATE fz_reports SET status='Closed', description='$temp_text' WHERE id='$report_id'"))
  {
    $removeOK=false;
  }
  if($removeOK)
  {
    $_SESSION["report_error"]="Zamknięto zgłoszenie.";
    echo "<script>window.location='../reports-list.php'</script>;";
    die();
  }
  else
  {
    $_SESSION["report_error"]="Wystąpił błąd przy zamykaniu zgłoszenia.";
    echo "<script>window.location='../report.php?v=".$report_id."'</script>;";
    die();
  }
?>
