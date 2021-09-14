<?php
require_once('C:\xampp\htdocs\PracaInz_Final\scripts\scriptConnect.php');

$temp_chall = $connect->query('SELECT id FROM fz_chall WHERE CURRENT_DATE()>=fz_chall.end_date AND fz_chall.status!="ENDED"');
$temp_status = "ENDED";

while($row = $temp_chall->fetch_assoc())
{
  $chall_id=$row["id"];

  $result = $connect->query("SELECT fz_videos.id as videos_id, fz_chall.id as chall_id, fz_chall.status as chall_status, fz_videos.user_id as user_id, fz_chall.start_date as start_date, fz_chall.end_date as end_date,
                              (SELECT
                                COUNT(fz_videos_likes.id)
                                FROM fz_videos_likes
                                WHERE fz_videos_likes.performance_id=videos_id AND fz_videos_likes.rating_date BETWEEN start_date AND end_date) as likes
                             FROM fz_videos
                             JOIN fz_chall
                             ON fz_videos.chall_id=fz_chall.id
                             WHERE fz_chall.id='$chall_id'
                             ORDER BY likes DESC, fz_videos.id ASC
                             LIMIT 3");
                             $winners = array();
                             $i=0;

  $connect->query("SET @position := 0;");

  while($row = $result->fetch_assoc())
  {
   $winners[0]=$row["chall_id"];
   $winners[1]=$row["videos_id"];
   $winners[2]=$row["user_id"];
   $winners[3]=$row["chall_status"];
   $winners[4]=$row["likes"];
   echo "Chall ID: ".$winners[0]. " Video ID: ".$winners[1]." User ID: ".$winners[2]." Likes: ".$winners[4];?><br><?php

   $connect->query("UPDATE fz_chall SET status='$temp_status' WHERE id='$chall_id'");
   $connect->query("INSERT INTO fz_chall_winners (chall_id, videos_id, user_id, position) VALUES (".$winners[0].", ".$winners[1].", ".$winners[2].", (@position := ifnull(@position, 0) + 1))");
  }
}

if(mysqli_error($connect)=="")
{
  $log_file = fopen("winners_logs.txt", "w");
  fwrite($log_file,date("Y-m-d H:m:s")." = Saving successful\n");
  fclose($log_file);
}
else
{
  $log_file = fopen("winners_logs.txt", "w");
  fwrite($log_file,date("Y-m-d H:m:s = ").mysqli_error($connect)."\n");
  fclose($log_file);
}

exit();
?>
