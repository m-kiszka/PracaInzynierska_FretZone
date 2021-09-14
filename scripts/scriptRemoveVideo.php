<?php
  session_start(); require_once('./scriptConnect.php');
  //require_once('./scriptFtpData.php');
  require_once('./scriptCheck_LoggedIn_Script.php');

  if(isset($_SESSION["perf_id"]))
  {
    $perf_id = $_SESSION["perf_id"];
    unset($_SESSION["perf_id"]);
  }

  $removeOK=true;
  //if(!$connect->query("DELETE FROM fz_videos WHERE id='$perf_id'"))
  if(!isset($_SESSION["perf_change"]))
  {
    echo "<script>window.location='../index.php'</script>;";
    die();
  }
  else
  {
    unset($_SESSION["perf_change"]);
  }

  if(!$connect->query("UPDATE fz_videos SET status='LOCKED' WHERE id='$perf_id'"))
  {
    $removeOK=false;
  }

  unset($_SESSION["perf_type"]);
  unset($_SESSION["perf_url"]);
  unset($_SESSION["perf_change"]);

  //LOKALNIE
  /*
  if($_SESSION["perf_type"]==0)
  {
    if(!unlink("../uploads/videos/".$_SESSION["perf_url"]))
    {
      $removeOK=false;
    }
  }
  */

  //FTP
  /*
  if($_SESSION["perf_type"]==0)
  {
    if(!ftp_delete($ftp_conn, '//PracaInz_Final/uploads/videos/'.$_SESSION["perf_url"]))
    {
      $removeOK=false;
    }
  }
  ftp_close($ftp_conn);
  */
  //FTP

  if($removeOK)
  {
    $_SESSION["video_error"]="Usunięto wykonanie.";
    echo "<script>window.location='../videos-list.php'</script>;";
    die();
  }
  else
  {
    $_SESSION["video_error"]="Wystąpił błąd przy usuwaniu wykonania.";
    echo "<script>window.location='../play.php?v=".$perf_id."'</script>;";
    die();
  }
?>
