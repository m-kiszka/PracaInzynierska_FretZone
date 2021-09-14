<?php
session_start(); require_once('./scriptConnect.php');
require_once('./scriptCheck_Moderator.php');

if(isset($_GET["id"]) && $_GET["id"]!="" && isset($rank_result) && $rank_result[0]!="" && $rank_result[0]>=3)
{
  $tag_id=$_GET["id"];
  $removeOK=true;
  if(!$connect->query("DELETE FROM fz_tags WHERE id='$tag_id'"))
  {
    $removeOK=false;
  }

  if($removeOK)
  {
    $_SESSION["event_error"]="Usunięto tag.";
    echo "<script>window.location='../tags-list.php'</script>;";
    die();
  }
  else
  {
    $_SESSION["event_error"]="Wystąpił błąd przy usuwaniu taga.";
    echo "<script>window.location='../tags-list.php'</script>;";
    die();
  }
}
else
{
  $_SESSION["event_error"]="Wystąpił błąd.";
  echo "<script>window.location='../tags-list.php'</script>;";
  die();
}
?>
