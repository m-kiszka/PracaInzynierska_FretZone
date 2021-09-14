<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <?php
    //sprawdzanie czy profil istnieje, w przeciwnym wypadku przekierowanie do strony 404
      if(isset($_SESSION["login"]) && ((isset($_GET["p"]) && ($_GET["p"]=="my" || $_GET["p"]=="me") || !isset($_GET["p"]))))
      {
        $profile_login=$_SESSION["login"];
      }
      elseif(isset($_GET['p']))
      {
        $profile_login = $_GET['p'];
        $profile_login = htmlspecialchars($profile_login);
      }
      else
      {
        ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
        die();
      }
      $result = $connect->query("SELECT * FROM fz_users WHERE login = '$profile_login'");
      $result = $result->fetch_assoc();

      if($result==null || $result=="")
      {
        ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
        die();
      }
//konto zawieszone
      if($result["user_rank"]==-1)
      {
        ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
        die();
      }
    ?>

    <title><?php echo $result["login"]; ?> - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>

    <!-- modal -->
      <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
          <span class="close">&times;</span>
          <?php if(isset($_SESSION["userid"])) { include("add-report.php"); } else { echo "Musisz się zalogować, aby wysłać zgłoszenie."; } ?>
        </div>
      </div>

      <div class="banner">
        <a title="Strona główna" href="./index.php">
          <div class="logo"></div>
        </a>
          <p class="logo-text"><?php echo $profile_login;?></p>
        <?php include('./scripts/scriptBannerButtons.php'); ?>
      </div>

  <div class="ul-container">
    <div class="grid">
      <div class="g-1">
        <div class="avatar">
          <!-- avatar -->
          <?php
            if($result["avatar_url"]!="")
            { ?>
              <img class="p_profile_img" src="<?php echo "./uploads/avatars/".$result["avatar_url"]; ?>" alt="profile_img">
     <?php  }
            else
            { ?>
                <img class="p_profile_img" src="./uploads/avatars/default.png" alt="profile_img">
     <?php  }
          ?>

        </div>
        <?php if(isset($_SESSION["login"]) && $_SESSION["login"]==$profile_login)
        { ?>
          <a href="./edit-profile.php" title="Edytuj profil"><button class="ep-submit" style="float: right; margin-right: 27px;">Edytuj profil</button></a>
        <?php } ?>
      </div>
      <div class="g-1">

      <!-- nazwa użytkownika -->
        <div class="prof-name"><h3 class="p_name"><?php echo $result["login"] ?> <i class='fas fa-flag' id="myBtn" title="Zgłoś użytkownika"></i></h3></div><br>

      <!-- imie nazwisko -->
        <?php if($result["name_surname"]!="") { echo $result["name_surname"]; ?><br><?php } ?><br>

      <!-- lokalizacja -->
        <?php if($result["localization"]!="") { echo "Lokalizacja: ".$result["localization"]; ?><br><?php } ?><br>

      <!-- wiek -->
        <?php if($result["birth_date"]!="")
              {
                $birth_date = new DateTime($result["birth_date"]);
                $today = new DateTime(date("Y-m-d"));
                $years  = $today->diff($birth_date);
                echo "Wiek: ".$years->format('%y');
              } ?>
      </div>

      <div class="achievements">
        <!-- medale -->
        <!-- zlote -->
        <img class="medal-icon" src="./images/icons/goldIcon.png" />
        <?php
          $temp_id = $result["id"];
          $medals = $connect->query("SELECT COUNT(id) FROM fz_chall_winners WHERE user_id='$temp_id' AND position=1");
          $medals = $medals->fetch_row();

          if(isset($medals[0]) && $medals[0]!=0)
          {
            echo $medals[0];
          }
          else
          {
            echo "0";
          }
        ?>
        <!-- srebrne -->
        <img class="medal-icon" src="./images/icons/silverIcon.png" />
        <?php
          $medals = $connect->query("SELECT COUNT(id) FROM fz_chall_winners WHERE user_id='$temp_id' AND position=2");
          $medals = $medals->fetch_row();
          if(isset($medals[0]) && $medals[0]!=0)
          {
            echo $medals[0];
          }
          else
          {
            echo "0";
          }
        ?>
        <!-- brazowe -->
        <img class="medal-icon" src="./images/icons/bronzeIcon.png" />
        <?php
          $medals = $connect->query("SELECT COUNT(id) FROM fz_chall_winners WHERE user_id='$temp_id' AND position=3");
          $medals = $medals->fetch_row();
          if(isset($medals[0]) && $medals[0]!=0)
          {
            echo $medals[0];
          }
          else
          {
            echo "0";
          }
        ?>
      </div>
    </div>

    <!-- drugi wiersz -->
          <br><br><br>Najnowsze wykonania:<br><br>
    <div class="grid2">
      <div class="g2-1">
        <?php
        $result_temp = $connect->query("SELECT id, name, url, added_date, type
                                   FROM fz_videos
                                   WHERE user_id='$temp_id' AND NOT status='LOCKED'
                                   ORDER BY added_date ASC
                                   LIMIT 5");

        while($row = $result_temp -> fetch_assoc())
        {
          if($row["type"]==0)
          {
            echo "<div class=\"prof-video i-new-clip\"><a href='./play.php?v=".$row["id"]."'>" ?>
            <video width="250" height="155" preload="metadata" oncontextmenu="return false;" disablePictureInPicture controlsList="nodownload">
              <source src="<?php echo "./uploads/videos/".$row["url"]; ?>#t=0.1" type="video/mp4">
            </video></a><?php
          }
          //film z youtube
          elseif($row["type"]==1)
          {
            echo "<div class=\"prof-video i-new-clip\"><a href='./play.php?v=".$row["id"]."'><img style=\"width: 250px; height: 150px;\" src='https://img.youtube.com/vi/".$row["url"]."/maxresdefault.jpg'></a>";
          }
          ?> </div> <?php
          echo $row["name"];
        }

        ?>
      </div>
        </div>
        <?php   echo "<br><br><br><a class=\"a-up see-more\" href='./videos-list.php?search_by_login=".$profile_login."'>Zobacz wszystkie</a><br>";
          ?>
    <!-- trzeci wiersz -->
    <div class="grid3">
      <div class="last-table-1">
              <br><br>Ostatnio brano udział w:</br>
        <table>
          <tr>
            <th>Nazwa wydarzenia</th>
          </tr>
        <?php
        $result_temp = $connect->query("SELECT fz_chall.id as chall_id, fz_chall.name as chall_name, fz_videos.chall_id, fz_videos.added_date
                                   FROM fz_videos
                                   JOIN fz_chall ON fz_videos.chall_id=fz_chall.id
                                   WHERE fz_videos.user_id='$temp_id' AND NOT fz_chall.status='LOCKED'
                                   ORDER BY fz_videos.added_date ASC
                                   LIMIT 5");

        while($row = $result_temp -> fetch_assoc())
        {
          echo "<tr><td><a href='./event.php?v=".$row["chall_id"]."'>".$row["chall_name"]."</a></td></tr>";
        }
        ?>
        </table>
      </div>
      <div class="last-table-2">
        <br><br>Najnowsze tabulatury:
        <table>
          <tr>
            <th>Tytuł</th>
            <th>Data</th>
          </tr>
        <?php
        $result_temp = $connect->query("SELECT id, name, added_date
                                   FROM fz_tabs
                                   WHERE user_id='$temp_id' AND NOT status='LOCKED'
                                   ORDER BY added_date ASC
                                   LIMIT 3");

        while($row = $result_temp -> fetch_assoc())
        {
          ?>
          <tr><td><a href="./tab.php?v=<?php echo $row["id"]; ?>"><?php echo $row["name"]; ?></a></td>
          <?php echo "<td>".$row["added_date"]."</td></tr>";
        }

        ?></table>
      </div>
    </div>
    <?php

    echo "<br><a class=\"see-more\" href='./tabs-list.php?search_by_login=".$profile_login."'>Zobacz wszystkie</a>";
    ?>
  </div>

  <script type="text/javascript">

    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];
    btn.onclick = function()
    {
      modal.style.display = "block";
    }

    span.onclick = function()
    {
      modal.style.display = "none";
    }

    window.onclick = function(event)
    {
      if (event.target == modal)
      {
        modal.style.display = "none";
      }
    }
  </script>

  <footer>
   <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
  </footer>
  </body>
</html>
