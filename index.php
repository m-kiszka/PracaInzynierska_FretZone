<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<?php
if(isset($_SESSION["userid"]))
{
  $id = $_SESSION["userid"];
  $rank_result = $connect->query("SELECT user_rank FROM fz_users WHERE id='$id'");
  unset($id);
  $rank_result = $rank_result->fetch_row();
  if(isset($rank_result) && $rank_result==-1)
  {
    ?> <script type="text/javascript">location.href = './scripts/scriptLogout.php';</script> <?php
    die();
  }
}
?>

<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Strona główna - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/fontawesome.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
  </head>
  <body>

    <?php include('./scripts/scriptCookiesBanner.html');?>

    <div class="banner">
      <a href="./index.php">
        <div class="logo"></div>
      </a>
      <p class="logo-text">FretZone</p>
      <?php
      include('./scripts/scriptBannerButtons.php');
      if(isset($rank_result) && $rank_result[0]>=3)
      { ?>
        <a href="./manage.php"><i class='icon fas fa-tachometer-alt'></i></a> <?php
      }
      ?>
    </div>
    <div class="i-container ul-container">
      <div class="m-main-container">
        <div class="m-container">
          <a href="./events-list.php?status=all">
            <div class="manage-item">
              <i class='fas fa-trophy'></i>
              <center><h4 class="m-h4">Wydarzenia</h4>
            </div>
          </a>
          <a href="./videos-list.php">
            <div class="manage-item">
              <i class='fas fa-file-video'></i>
              <h4 class="m-h4">Wykonania</h4>
            </div>
          </a>
          <a href="./tabs-list.php">
            <div class="manage-item">
              <i class='fas fa-pen-alt'></i>
              <h4 class="m-h4">Tabulatury</h4></center>
            </div>
          </a>
        </div>
      </div>

      <!-- <div class="error">
          <?php
            /* if(isset($_SESSION["upload_error"]))
            {
              echo $_SESSION["upload_error"];
              unset($_SESSION["upload_error"]);
            } */
        ?>
      </div> -->

      <h2>Ostatni zwycięzcy</h2>
          <?php
           //OSTATNI ZWYCIĘZCY
           $result = $connect->query("SELECT fz_chall_winners.id as id,
                                             fz_chall_winners.chall_id as event_id,
                                             fz_chall_winners.videos_id as video_id,
                                             fz_chall_winners.user_id as user_id,
                                             fz_chall_winners.position as pos,
                                             fz_chall.name as event_name,
                                             fz_videos.name as video_name,
                                             fz_videos.url as video_url,
                                             fz_videos.type as video_type,
                                             fz_users.login as login,
                                             fz_users.avatar_url as avatar
                                      FROM fz_chall_winners
                                      JOIN fz_chall ON fz_chall_winners.chall_id = fz_chall.id
                                      JOIN fz_videos ON fz_chall_winners.videos_id = fz_videos.id
                                      JOIN fz_users ON fz_chall_winners.user_id = fz_users.id
                                      WHERE fz_chall_winners.chall_id=(SELECT MAX(fz_chall_winners.chall_id) FROM fz_chall_winners)
                                      ORDER BY fz_chall_winners.id ASC
                                      LIMIT 3");

           $num_rows = mysqli_num_rows($result);

           if($num_rows>0)
           {
             $i=0;
             $video_name = array();
             $event_info = array();
             while($i<$num_rows)
             {
               $row = $result->fetch_assoc();

               $event_info[0] = $row["event_id"];
               $event_info[1] = $row["event_name"];

               $video_info[$i][0] = $row["video_id"];
               $video_info[$i][1] = $row["video_name"];
               $video_info[$i][2] = $row["video_url"];
               $video_info[$i][3] = $row["video_type"];
               $video_info[$i][4] = $row["login"];
               $video_info[$i][5] = $row["avatar"];
               $video_info[$i][6] = $row["pos"];
               $i+=1;
             }

             //nazwa wydarzenia z odnosnikiem
             echo "<center><a style=\"width: 100%; text-align: center; margin-top:15px;\" href='./event.php?v=".$event_info[0]."'>".$event_info[1]."</a></center><br>"; ?>

          <div class="popular-tabs" id="winners">
            <center>
             <div class="winner"> <?php

             //zwyciezca numer 2
             if(isset($video_info[1][0]))
             {
               //nazwa filmu
               // echo "<label style=\" position: relative; margin-left: 60px;\">".$video_info[1][1]."</label>";
               //nazwa autora
               // echo "<a style=\" float: center !important;\" href='./profile.php?p=".$video_info[1][4]."'>".$video_info[1][4]."</a><br>";
               //avatar z odnosnikiem do profilu
               if($video_info[1][5]!=null)
               {
                 ?> <a style="float: center !important;" href="./profile.php?p=<?php echo $video_info[1][4]; ?>"><img class="i-winner gold" src="./uploads/avatars/<?php echo $video_info[1][5]; ?>"></a> <?php
               }
               else
               {
                 ?> <a style="float: center !important;" href="./profile.php?p=<?php echo $video_info[1][4]; ?>"><img class="i-winner gold" src="./uploads/avatars/default.png"></a> <?php
               }
               //wyswietlenie medalu w zaleznosci od pozycji
               switch($video_info[1][6])
               {
                 case 1:
                   echo "<img class=\"medals medal-gold\" src='./images/icons/goldIcon.png'>";
                 break;
                 case 2:
                   echo "<img class=\"medals medal-silver\" src='./images/icons/silverIcon.png'>";
                 break;
                 case 3:
                   echo "<img class=\"medals medal-bronze\" src='./images/icons/bronzeIcon.png'>";
                 break;
               }
               //film z serwera
               if($video_info[1][3]==0)
               { ?>
                 <a href='./play.php?v=<?php echo $video_info[1][0] ?>'>
                 <video width="250" height="150" preload="metadata" oncontextmenu="return false;" disablePictureInPicture controlsList="nodownload">
                   <source src="<?php echo "./uploads/videos/".$video_info[1][2]; ?>#t=0.1" type="video/mp4">
                 </video></a> <?php
               }
               //film z youtube
               elseif($video_info[1][3]==1)
               {
                 echo "<a style=\" float: center !important;\" href='./play.php?v=".$video_info[1][0]."'><img class=\"i-video\" style=\"border-color: silver;\" src='https://img.youtube.com/vi/".$video_info[1][2]."/maxresdefault.jpg'></a>";
               }
               ?> </div> <?php
             } ?>

             <div class="winner"> <?php

             //zwyciezca numer 3
             if(isset($video_info[2][0]))
             {

               //nazwa autora
               // echo "<a style=\" float: center !important;\" href='./profile.php?p=".$video_info[2][4]."'>".$video_info[2][4]."</a><br>";
               //avatar z odnosnikiem do profilu
               if($video_info[2][5]!=null)
               {
                 ?> <a style="float: center !important;" href="./profile.php?p=<?php echo $video_info[2][4]; ?>"><img class="i-winner gold" src="./uploads/avatars/<?php echo $video_info[2][5]; ?>"></a> <?php
               }
               else
               {
                 ?> <a style="float: center !important;" href="./profile.php?p=<?php echo $video_info[2][4]; ?>"><img class="i-winner gold" src="./uploads/avatars/default.png"></a> <?php
               }
               //wyswietlenie medalu w zaleznosci od pozycji
               switch($video_info[2][6])
               {
                 case 1:
                   echo "<img class=\"medals medal-gold\" src='./images/icons/goldIcon.png'>";
                 break;
                 case 2:
                   echo "<img class=\"medals medal-silver\" src='./images/icons/silverIcon.png'>";
                   break;
                 case 3:
                   echo "<img class=\"medals medal-bronze\" src='./images/icons/bronzeIcon.png'>";
                 break;
               }
               //film z serwera
               if($video_info[2][3]==0)
               { ?>
                 <a href='./play.php?v=<?php echo $video_info[2][0] ?>'>
               <video class="i-video" width="250" height="150" preload="metadata" oncontextmenu="return false;" disablePictureInPicture controlsList="nodownload">
                   <source src="<?php echo "./uploads/videos/".$video_info[2][2]; ?>#t=0.1" type="video/mp4">
               </video></a> <?php
               }
               //film z youtube
             elseif($video_info[2][3]==1)
               {
                 echo "<a href='./play.php?v=".$video_info[2][0]."'><img class=\"i-video\" style=\"border-color: bronze;\" src='https://img.youtube.com/vi/".$video_info[2][2]."/maxresdefault.jpg'></a>";
               }
               //nazwa filmu
               // echo "<label style=\" position: relative; margin-left: 60px;\">".$video_info[2][1]."</label>";
             }
             ?></div><?php
           }
           ?>
           <div class="winner"> <?php

           //zwyciezca numer 1
           if(isset($video_info[0][0]))
           {
             //nazwa autora
             // echo "<a style=\" float: center !important;\" href='./profile.php?p=".$video_info[0][4]."'>".$video_info[0][4]."</a><br>";
             //nazwa filmu
             // echo "<label style=\" position: relative; margin-left: 60px;\>".$video_info[0][1]."</label>";

             //avatar z odnosnikiem do profilu
             if($video_info[0][5]!=null)
             {
               echo "<a class=\"i-p\" href='./profile.php?p=".$video_info[0][4]."'><img class=\"i-winner gold\" src='./uploads/avatars/".$video_info[0][5]."'></a>";
             }
             else
             {
               echo "<a class=\"i-p\" href='./profile.php?p=".$video_info[0][4]."'><img class=\"i-winner gold\" src='./uploads/avatars/default.png'></a>";
             }
             //wyswietlenie medalu w zaleznosci od pozycji
             switch($video_info[0][6])
             {
               case 1:
                 echo "<img class=\"medals medal-gold\" src='./images/icons/goldIcon.png'>";
                 break;
               case 2:
                 echo "<img class=\"medals medal-silver\" src='./images/icons/silverIcon.png'>";
                 break;
               case 3:
                 echo "<img class=\"medals medal-bronze\" src='./images/icons/bronzeIcon.png'>";
               break;
             }
             //film z serwera
             if($video_info[0][3]==0)
             { ?>
               <a href='./play.php?v=<?php echo $video_info[0][0] ?>'>
               <video class="i-video" width="250" height="150" preload="metadata" oncontextmenu="return false;" disablePictureInPicture controlsList="nodownload">
                 <source src="<?php echo "./uploads/videos/".$video_info[0][2] ?>#t=0.1" type="video/mp4">
               </video></a> <?php
             }
             //film z youtube
             elseif($video_info[0][3]==1)
             {
               echo "<a href='./play.php?v=".$video_info[0][0]."'><img class=\"i-video\" src='https://img.youtube.com/vi/".$video_info[0][2]."/maxresdefault.jpg'></a>";
             }
             ?> </div></div> <?php
           }
           else
           {
             ?><div class="popular-tabs" id="winners">
             <div class="index-align"><?php echo "Brak nowych wydarzeń.";?></div>
           </center>
           </div><?php
           }

          ?>

      <h2>Najnowsze wykonania</h2>
      <div id="i-new-clip-id" class="popular-tabs">
        <?php
        $result = $connect->query("SELECT id, name, url, added_date, type
                                   FROM fz_videos
                                   WHERE NOT status='LOCKED'
                                   ORDER BY added_date ASC
                                   LIMIT 3");

        while($row = $result -> fetch_assoc())
        {
          if($row["type"]==0)
          {
            echo "<div class=\"i-descr\"><a href='./play.php?v=".$row["id"]."'>"?>
            <video width="250" height="150" preload="metadata" oncontextmenu="return false;" disablePictureInPicture controlsList="nodownload">
              <source src="<?php echo "./uploads/videos/".$row["url"]; ?>#t=0.1" type="video/mp4">
            </video></a> <?php
          }
          //film z youtube
          elseif($row["type"]==1)
          {
            echo "<div class=\"i-descr\"><a href='./play.php?v=".$row["id"]."'><img class=\"i-new-clip\" src='https://img.youtube.com/vi/".$row["url"]."/maxresdefault.jpg'></a>";
          }
          echo "<p class=\"i-row\">".$row["name"]."</p></div>";?><?php
        }
        ?>

      </div>
        <?php echo "<a href='./videos-list.php?order=added_date'>Zobacz wszystkie najnowsze wykonania</a>"; ?>

      <h2>Kończące się wydarzenia</h2>
      <div class="popular-tabs">
        <table>
          <tr>
            <th>Nazwa wydarzenia</th>
            <th>Data zakończenia</th>
          </tr>
        <?php

        //KOŃCZĄCE SIĘ WYDARZENIA
        $result = $connect->query("SELECT id, name, end_date, start_date, status
                                   FROM fz_chall
                                   WHERE status!='LOCKED' AND CURRENT_DATE() between start_date and end_date
                                   ORDER BY end_date ASC
                                   LIMIT 3");

        $i = 0;
        while($row = $result -> fetch_assoc())
        {
          ?><tr><td><center><a class="i-a" href="./event.php?v=<?php echo $row["id"]; ?>"><?php echo $row["name"]; ?></a></center></td>
            <td><?php echo $row["end_date"]."</td></tr>";
          $i++;

        }
        ?> </table> <?php
        if($i==0)
        {
          ?><div class="index-align"></br><?php echo "Nie ma więcej wydarzeń."; ?></div><?php
        }

        ?>

      </div>
      <?php echo "<a href='./events-list.php?status=active&order=end_date&order_dir=asc'>Zobacz wszystkie kończące się wydarzenia</a>"; ?>

      <h2>Najpopularniejsze aktywne wydarzenia</h2>
      <div class="popular-tabs">
        <table>
          <tr>
            <th>Nazwa wydarzenia</th>
            <th>Liczba zgłoszeń</th>
          </tr>
        <?php
        $result = $connect->query("SELECT fz_chall.id, fz_chall.name, COUNT(fz_videos.id) as participants, fz_chall.start_date, fz_chall.end_date, fz_chall.status
                                   FROM fz_chall
                                   LEFT JOIN fz_videos
                                   ON fz_chall.id=fz_videos.chall_id
                                   WHERE fz_chall.status!='LOCKED' AND CURRENT_DATE() between fz_chall.start_date and fz_chall.end_date
                                   GROUP BY fz_chall.id, fz_chall.name
                                   ORDER BY participants DESC
                                   LIMIT 3");

        $i = 0;
        while($row = $result -> fetch_assoc())
        {
          ?><tr><td><a class="i-a" href="./event.php?v=<?php echo $row["id"]; ?>"><?php echo $row["name"]; ?></a></td>
          <td><?php echo $row["participants"]."</br>"?></td></tr><?php
          $i++;
        }
        ?></table><?php
        if($i==0)
        {
          ?><div class="index-align"></br><?php echo "Nie ma więcej wydarzeń."; ?></div><?php
        }
        ?>

      </div>
      <?php         echo "<a href='./events-list.php?status=all'>Zobacz wszystkie wydarzenia</a>"; ?>

      <h2>Nadchodzące wydarzenia</h2>
      <div class="popular-tabs">
        <table>
          <tr>
            <th>Nazwa wydarzenia</th>
            <th>Data rozpoczęcia</th>
          </tr>
        <?php
        //NADCHODZĄCE WYDARZENIA
        $result = $connect->query("SELECT id, name, start_date
                                   FROM fz_chall
                                   WHERE start_date>CURRENT_DATE()
                                   ORDER BY start_date ASC
                                   LIMIT 3");

        $i = 0;
        while($row = $result -> fetch_assoc())
        {
          ?>
        <td><a class="i-a" href="./event.php?v=<?php echo $row["id"]; ?>"><?php echo $row["name"]; ?></a></center></td>
        <td><?php echo $row["start_date"]; ?></td></tr>
          <?php
          $i++;
        }
        ?></table><?php
        if($i==0)
        {
          ?><div class="index-align"></br><?php echo "Nie ma więcej wydarzeń."; ?></div><?php
        }
        ?>
      </div>
      <?php         echo "<a href='./events-list.php?status=upcoming&order=start_date'>Zobacz wszystkie nadchodzące wydarzenia</a>"; ?>

      <h2>Najnowsze tabulatury</h2>
      <div class="popular-tabs">
        <table>
          <tr>
            <th>Nazwa</th>
            <th>Data utworzenia</th>
          </tr>

        <?php
        $result = $connect->query("SELECT id, name, added_date
                                   FROM fz_tabs
                                   WHERE NOT status='LOCKED'
                                   ORDER BY added_date ASC
                                   LIMIT 3");

        $i = 0;
        while($row = $result -> fetch_assoc())
        {
          ?>
          <tr><td><a class="i-a" href="./tab.php?v=<?php echo $row["id"]; ?>"><?php echo $row["name"]; ?></a></td>
          <?php echo "<td>".$row["added_date"]."</td></tr></br>";
          $i++;
        }
        ?></table><?php
        if($i==0)
        {
          ?><div class="index-align"></br><?php echo "Nie ma więcej tabulatur."; ?></div><?php
        }

        ?>
      </div>
      <?php echo "<a href='./tabs-list.php?order=id&order_dir=desc'>Zobacz wszystkie najnowsze tabulatury</a>";

      if(isset($_SESSION["upload_error"]) && $_SESSION["upload_error"]!="") {
         ?><script>alert("<?php echo $_SESSION["upload_error"];?>")</script><?php
         unset($_SESSION["upload_error"]);
       } ?>
    </div>
  </div>
  </body>
  <footer>
   <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
  </footer>
</html>
