<?php
  session_start();
  require_once('./scriptConnect.php');

  $perf_id = $_REQUEST["req_perf_id"];
  $user_id = $_SESSION["userid"];

  $result = $connect->query("SELECT * FROM fz_videos_likes WHERE performance_id='$perf_id' AND user_id='$user_id'");
  $result = $result->fetch_row();

  if($result==null)
  {
    $connect->query("INSERT INTO fz_videos_likes (performance_id, user_id, rating_date) VALUES ('$perf_id','$user_id',CURRENT_TIMESTAMP)");
    echo 1;
  }
  //zabrano polubienie
  else
  {
    $connect->query("DELETE FROM fz_videos_likes WHERE performance_id='$perf_id' AND user_id='$user_id'");
    echo 0;
  }
?>
