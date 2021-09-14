<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<script src="./resources/jquery-3.5.1.min.js"></script>

<?php
$perf_id=$_GET["v"];
//pobieranie danych wykonania oraz nazwy i avatara twórcy
$result = $connect->query("SELECT fz_videos.name, fz_videos.description, fz_videos.url, fz_videos.added_date, fz_videos.type, fz_videos.status, fz_videos.chall_id, fz_users.login, fz_users.avatar_url, fz_videos.id, fz_users.avatar_url
                           FROM fz_videos
                           JOIN fz_users
                           ON fz_videos.user_id=fz_users.id
                           WHERE fz_videos.id='$perf_id'");
$result = $result->fetch_row();

if($result==null || $result=="")
{
  ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
  die();
}
elseif($result[5]!="OK" && $result[5]!="ok")
{
  ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
  die();
}

if(isset($_SESSION["perf_change"]))
{
  unset($_SESSION["perf_change"]);
}

//dodawanie wyświetleń filmu do bazy
if(!isset($_COOKIE["visit"]))
{
  $json_array=array($perf_id=>"1");
  if(setcookie("visit", json_encode($json_array), time() + (60 * 60 * 24 * 30), "/"))
  {
    $connect->query("INSERT INTO fz_videos_views (performance_id, visit_date) VALUES ('$perf_id', CURRENT_TIMESTAMP)");
  }
}
else
{
  $cookie_data = json_decode($_COOKIE["visit"], true);
  if(!array_key_exists($perf_id, $cookie_data))
  {
    $cookie_data[$perf_id]="1";
    //$cookie_data = array_push($json_array);
    if(setcookie("visit", json_encode($cookie_data), time() + (60 * 60 * 24 * 30), "/"))
    {
      $connect->query("INSERT INTO fz_videos_views (performance_id, visit_date) VALUES ('$perf_id', CURRENT_TIMESTAMP)");
    }
  }
}

//zbierz z bazy id tagów
$temp_tags_id = $connect->query("SELECT tag_id FROM fz_videos_tags WHERE performance_id='$perf_id'");

$tags_id = array();
$tags = array();
//dodaj każdy tag (rekord z tabeli) do tablicy
while($row = $temp_tags_id->fetch_assoc())
{
  array_push($tags_id, $row["tag_id"]);
}

//dodaj do tablicy każdy tag, którego id znajduje się w innej tablicy
foreach($tags_id as $tag)
{
    $tag_result = $connect->query("SELECT name FROM fz_tags WHERE id='$tag'");
    $tag_result = $tag_result->fetch_row();
    array_push($tags, $tag_result[0]);
}

//zbierz liczbę wyświetleń z bazy
$views=$connect->query("SELECT COUNT(id) FROM fz_videos_views WHERE performance_id='$perf_id'");
$views=$views->fetch_row();

//zbierz liczbę polubień z bazy
$likes=$connect->query("SELECT COUNT(id) FROM fz_videos_likes WHERE performance_id='$perf_id'");
$likes=$likes->fetch_row();
?>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <title><?php echo $result[0]; ?> - FretZone</title>
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./css/style2.css">
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


    <!-- banner -->
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text"><?php echo $result[0]; ?></p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <br><br><br>

    <!-- container -->
    <div class="p-container">
      <!-- <div class="error">
        <?php /* if(isset($_SESSION["add_report_error"]) && $_SESSION["add_report_error"]!="") {  echo $_SESSION["add_report_error"]; unset($_SESSION["add_report_error"]); } */ ?>
      </div> -->
      <div class="p-left">
      <div class="play">
        <!-- wyświetlanie okna z filmem -->
        <?php if($result[4]==0) { ?> <!-- jeśli plik mp4 -->

          <!-- sprawdzic czy mozna w css zedytowac -->
      <center><video controls width="120%" height="350" oncontextmenu="return false;" controlsList="nodownload">
          <source src="<?php echo "./uploads/videos/".$result[2]; ?>" type="video/mp4">
    	       Brak obsługi video przez przeglądarkę, której używasz.
        </video></center>
        <?php }
        elseif($result[4]==1) { ?> <!-- jeśli link do filmu youtube -->

          <!-- sprawdzic czy mozna w css zedytowac -->
          <center><iframe width="120%" height="300px;"
            style="margin-left: 8px" src="https://www.youtube.com/embed/<?php echo $result[2]; ?>">
          </iframe></center>
        <?php } ?>

    <!-- sprawdzanie czy daliśmy polubienie -->
    <?php if(isset($_SESSION["userid"]))
          {
            $user_id=$_SESSION["userid"];
            $like_result = $connect->query("SELECT id
                                       FROM fz_videos_likes
                                       WHERE performance_id='$perf_id' AND user_id='$user_id'");
            $like_result = $like_result->fetch_row();

            if($like_result==null)
            {
              $like_result[0]="";
            }
          }
          else
          {
            $like_result[0]="";
          }
    ?>

    <!-- polubienie filmu -->
    <script type="text/javascript">
    //polubienia po załadowaniu strony
      $(window).on('load', function()
      {
        <?php if(isset($_SESSION["userid"]))
        { ?>
          var likes = <?php echo $likes[0]; ?>;
          var perf_id = "<?php echo $perf_id; ?>";
          let temp = "<?php echo $like_result[0]; ?>";

          if(temp!="")
          {
            $("#like_icon").attr("src", "./images/icons/fireIconBlur.png");
          }
          else
          {
            $("#like_icon").attr("src", "./images/icons/fireIconBlur0.png");
          }

          //przy kliknięciu w ikonę
          $("#like_icon").click(function()
          {
            $.get("./scripts/scriptPlayLike.php", {req_perf_id: perf_id}).done(function(data)
            {
              if(data==1)
              {
                $("#like_icon").attr("src", "./images/icons/fireIconBlur.png");
                $("label[for='likes_count']").text(++likes);
              }
              else
              {
                $("#like_icon").attr("src", "./images/icons/fireIconBlur0.png");
                $("label[for='likes_count']").text(--likes);
              }
            });
          });
        <?php } ?>
      });
    </script>
<center><span class="p-icons">
    <!-- wyświetlenia filmu -->
    <img title="Wyświetlenia" src="./images/icons/eyeIcon.png" width="25" height="25"/ name="view_icon" id="view_icon"> <label><?php echo $views[0]; ?></label>
    <!-- polubienia filmu -->
    <img title="Polubienia" src="./images/icons/fireIconBlur0.png" width="25" height="25"/ name="like_icon" id="like_icon"> <label for="likes_count"><?php echo $likes[0]; ?></label>
    <?php if(!isset($_SESSION["userid"])) { echo "</br>Musisz się zalogować, aby polubić wykonanie."; } ?>
</span></center>
  </div>
</div>
  <div class="p-right">
    <!-- tytuł -->
    <h2 class="p_title"><?php echo $result[0]; ?> <i class='fas fa-flag' id="myBtn" title="Zgłoś zawartość"></i></h2>
    <!-- autor -->

    <?php
    if($result[10]!=null)
    {
      echo "<a href='./profile.php?p=".$result[7]."'><img class=\"i-winner\" src='./uploads/avatars/".$result[10]."'></a>";
    }
    else
    {
      echo "<a href='./profile.php?p=".$result[7]."'><img class=\"i-winner\" src='./uploads/avatars/default.png'></a>";
    }
    ?>

    <h3 class="p_author"><a href="./profile.php?p=<?php echo $result[7]; ?>"><?php echo $result[7]; ?></a></h3><br>
    <!-- opis, jesli istnieje -->
    <?php if($result[1]!="")
    { ?>
      <span class="p-desc"><?php echo $result[1]; ?></span>
    <?php } ?>

    <!-- wydarzenie/ jeśli film jest zgłoszeniem -->
    <?php
    if($result[6]!="")
    {
      $event = $connect->query("SELECT fz_chall.id, fz_chall.name
                                FROM fz_chall
                                JOIN fz_videos
                                ON fz_chall.id=fz_videos.chall_id
                                WHERE fz_videos.id='$perf_id' AND fz_chall.id=fz_videos.chall_id");
      $event = $event->fetch_row();
      ?> <p><?php echo "To wykonanie jest zgłoszeniem na: "; ?> <a href="./event.php?v=<?php echo $event[0];?>"><?php echo $event[1] ?></a></p><br><?php
    }
    ?>
    <br>
    <!-- tagi -->
    <?php
    foreach($tags as $tag)
    { ?>
      <label class="tag"><a href="./videos-list.php?search_by_tag=<?php echo $tag ?>"><?php echo $tag; ?></a></label> <?php
    }

    if(isset($_SESSION["userid"]))
    {
      $temp_var=$_SESSION["userid"];
      $user_rank = $connect->query("SELECT user_rank FROM fz_users WHERE id='$temp_var'");
      $user_rank = $user_rank->fetch_row();

      if($_SESSION["login"]==$result[7] || $user_rank[0]>=3)
      {
        $_SESSION["perf_id"]=$result[9];
        $_SESSION["perf_name"]=$result[0];
        $_SESSION["perf_desc"]=$result[1];
        $_SESSION["perf_type"]=$result[4];
        $_SESSION["perf_url"]=$result[2];
        ?><br><br><center><input type="submit" class="ep-submit submit-edit" value="Edytuj wykonanie" onclick="edit_event()" />
        <input type="submit" class="ep-submit submit-delete" value="Usuń wykonanie" onclick="remove_event()" /></center>
        <?php
      }
      else
      {
        if(isset($_SESSION["perf_id"]))
        {
          unset($_SESSION["perf_id"]);
        }
        if(isset($_SESSION["perf_name"]))
        {
          unset($_SESSION["perf_name"]);
        }
        if(isset($_SESSION["perf_desc"]))
        {
          unset($_SESSION["perf_desc"]);
        }
        if(isset($_SESSION["perf_type"]))
        {
          unset($_SESSION["perf_type"]);
        }
        if(isset($_SESSION["perf_url"]))
        {
          unset($_SESSION["perf_url"]);
        }
      }
    }
    ?>
        <script>
        function edit_event()
          {
            window.location='./edit-video.php';
            die();
          }
          function remove_event()
          {
            window.location='./remove-video.php';
            die();
          }
        </script>
      </div>
    </div>
      <!-- menu mobilne -->
        <nav class="nav-bottom">
          <a href="#" class="nav-item">
          <i class='icon icon-logout fas fa-power-off'></i>
            <span class="menu-text">Wyloguj</span>
          </a>
          <a href="#" class="nav-item">
          <i class='icon fas fa-cog'></i>
            <span class="menu-text">Ustawienia</span>
          </a>
          <a href="#" class="nav-item">
          <i class='icon fas fa-user-alt'></i>
            <span class="menu-text">Mój profil</span>
          </a>
        <!-- <a href="./moderator.php"><i class='icon fas fa-tachometer-alt'></i></a> -->
      </nav>
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

    <!-- Komunikat o błędzie -->
    <?php
    if(isset($_SESSION["add_report_error"]) && $_SESSION["add_report_error"]!="") {
       ?><script>alert("<?php echo $_SESSION["add_report_error"];?>")</script><?php
       unset($_SESSION["add_report_error"]);
     } ?>
     <footer>
      <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
     </footer>
  </body>
</html>
