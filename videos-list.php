<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <meta charset="utf-8">
    <title>Lista wykonań - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Lista wykonań</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <?php
      $count_video=0;
      $search_QUERY="";
      if(!isset($_GET["status"]))
      {
        $_GET["status"]="all";
      }

      if(isset($_SESSION["video_error"]) && $_SESSION["video_error"])
      {
        echo $_SESSION["video_error"];
        unset($_SESSION["video_error"]);
      }

      //odczytywanie na której stronie użytkownik jest
      if(isset($_GET["p"]) && $_GET["p"]!="" && $_GET["p"]>0)
      {
        $page = $_GET["p"];
      }
      else
      {
        $page=1;
      }

      //zapytanie od nazwy wykonania
      if(isset($_GET["search_by_name"]) && $_GET["search_by_name"]!="")
      {
        if(preg_match("/[^A-Za-z0-9_'.!?&()+: -]/", $_GET["search_by_name"]))
        {
          $search_by_name_QUERY="";
          $search_by_name="";
          $search_by_name_GET="";
        }
        else
        {
          $search_by_name_QUERY="fz_videos.name LIKE '%".$_GET["search_by_name"]."%'";
          $search_by_name=$_GET["search_by_name"];
          $search_by_name_GET="&search_by_name=".$search_by_name;
        }
      }
      else
      {
        $search_by_name_QUERY="";
        $search_by_name="";
        $search_by_name_GET="";
      }

      //zapytanie od loginu
      if(isset($_GET["search_by_login"]) && $_GET["search_by_login"]!="")
      {
        if(preg_match("/[^A-Za-z0-9_-]/", $_GET["search_by_login"]))
        {
          $search_by_login_QUERY="";
          $search_by_login="";
          $search_by_login_GET="";
        }
        else
        {
          $search_by_login_QUERY="fz_users.login LIKE '%".$_GET["search_by_login"]."%'";
          $search_by_login=$_GET["search_by_login"];
          $search_by_login_GET="&search_by_login=".$search_by_login;
        }
      }
      else
      {
        $search_by_login_QUERY="";
        $search_by_login="";
        $search_by_login_GET="";
      }

      //zapytanie od tagu
      if(isset($_GET["search_by_tag"]) && $_GET["search_by_tag"]!="")
      {
        if(!preg_match("/^[A-Za-z0-9,-]+$/", $_GET["search_by_tag"]))
        {
          $tag_join_QUERY="";
          $search_by_tag_QUERY="";
          $search_by_tag="";
          $search_by_tag_GET="";
        }
        else
        {
          $search_by_tag=$_GET["search_by_tag"];
          $tag_join_QUERY="JOIN fz_videos_tags ON fz_videos.id = fz_videos_tags.performance_id JOIN fz_tags ON fz_videos_tags.tag_id=fz_tags.id";
          $search_by_tag_QUERY="fz_tags.name LIKE '%".$_GET["search_by_tag"]."%'";
          $search_by_tag=$_GET["search_by_tag"];
          $search_by_tag_GET="&search_by_tag=".$search_by_tag;
        }
      }
      else
      {
        $tag_join_QUERY="";
        $search_by_tag_QUERY="";
        $search_by_tag="";
        $search_by_tag_GET="";
      }

      if(isset($_GET["search_by_event"]) && $_GET["search_by_event"]!="")
      {
        if(is_numeric($_GET["search_by_event"]))
        {
          $search_by_event_JOIN="JOIN fz_chall ON fz_videos.chall_id=fz_chall.id";
          $search_by_event_QUERY="fz_videos.chall_id=".$_GET["search_by_event"];
          $search_by_event_GET=$_GET["search_by_event"];
          $search_by_event=$_GET["search_by_event"];
        }
        else
        {
          $search_by_event_JOIN="";
          $search_by_event_QUERY="";
          $search_by_event_GET="";
          $search_by_event="";
        }
      }
      else
      {
        $search_by_event_JOIN="";
        $search_by_event_QUERY="";
        $search_by_event_GET="";
        $search_by_event="";
      }

//tworzenie zapytania od wyszukiwania
      if($search_by_tag_QUERY!="" || $search_by_name_QUERY!="" || $search_by_login_QUERY!="" || $search_by_event_QUERY!="")
      {
        //dodawanie do zapytania tagu
        if($search_by_tag_QUERY!="")
        {
          if($_GET["status"]=="created")
          {
            $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_tag_QUERY;
          }
          else
          {
            if($search_QUERY=="")
            {
              $search_QUERY=$search_QUERY."WHERE NOT fz_videos.status='LOCKED' AND ".$search_by_tag_QUERY;
            }
            else
            {
              $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_tag_QUERY;
            }
          }
        }
        //dodawanie zapytania nazwy
        if($search_by_name_QUERY!="")
        {
          if($_GET["status"]=="created")
          {
            $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_name_QUERY;
          }
          else
          {
            if($search_QUERY=="")
            {
              $search_QUERY=$search_QUERY."WHERE NOT fz_videos.status='LOCKED' AND ".$search_by_name_QUERY;
            }
            else
            {
              $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_name_QUERY;
            }
          }
        }
        //dodawanie zapytania loginu
        if($search_by_login_QUERY!="")
        {
          if($_GET["status"]=="created")
          {
            $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_login_QUERY;
          }
          else
          {
            if($search_QUERY=="")
            {
              $search_QUERY=$search_QUERY."WHERE NOT fz_videos.status='LOCKED' AND ".$search_by_login_QUERY;
            }
            else
            {
              $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_login_QUERY;
            }
          }
        }
        //dodawanie do zapytania wydarzenia
        if($search_by_event_QUERY!="")
        {
          if($_GET["status"]=="created")
          {
            $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_event_QUERY;
          }
          else
          {
            if($search_QUERY=="")
            {
              $search_QUERY=$search_QUERY."WHERE NOT fz_videos.status='LOCKED' AND ".$search_by_event_QUERY;
            }
            else
            {
              $search_QUERY=$search_QUERY." AND NOT fz_videos.status='LOCKED' AND ".$search_by_event_QUERY;
            }
          }
        }
      }
      else
      {
        if($_GET["status"]=="created" || $_GET["status"]=="liked")
        {
          $search_QUERY="AND NOT fz_videos.status='LOCKED'";
        }
        else
        {
          $search_QUERY="WHERE NOT fz_videos.status='LOCKED'";
        }
      }

      //po czym sortować
      if(isset($_GET["order"]) && $_GET["order"]!="")
      {
        $order = $_GET["order"];
        $order_GET="&order=".$order;

        if($order!="id" && $order!="name" && $order!="added_date" && $order!="end_date" && $order!="login")
        {
          $order="id";
          $order_GET="&order=id";
        }
      }
      else
      {
        $order="id";
        $order_GET="&order=id";
      }

      //w którą stronę sortować
      if(isset($_GET["order_dir"]) && $_GET["order_dir"]!="")
      {
        $order_dir = $_GET["order_dir"];
        $order_dir_GET="&order_dir=".$order_dir;

        if($order_dir!="asc" && $order_dir!="desc")
        {
          $order_dir="asc";
          $order_dir_GET="&order_dir=asc";
        }
      }
      else
      {
        $order_dir="asc";
        $order_dir_GET="&order_dir=asc";
      }

      //czy użytkownik jest zalogowany
      if(isset($_SESSION["userid"]))
      {
        $user_id = $_SESSION["userid"];
      }
      else
      {
        $user_id=NULL;
      }

      $page_start = 10*$page-10;

      $result=null;

      //wybrany status wydarzeń
      if(isset($_GET["status"]) && $_GET["status"]!="")
      {
        //wszystkie
        if($_GET["status"]=="all")
        {
          $status=$_GET["status"];
          $status_GET = "&status=all";
          $result = $connect->query("SELECT fz_videos.id, fz_videos.name, fz_videos.added_date, fz_users.login, fz_videos.status
                                     FROM fz_videos
                                     JOIN fz_users
                                     ON fz_videos.user_id=fz_users.id
                                     $tag_join_QUERY
                                     $search_by_event_JOIN
                                     $search_QUERY
                                     ORDER BY $order $order_dir
                                     LIMIT $page_start, 11");
        }
        //stworzono
        elseif($_GET["status"]=="created" && isset($user_id))
        {
          $status=$_GET["status"];
          $status_GET = "&status=created";
          $result = $connect->query("SELECT fz_videos.id, fz_videos.name, fz_videos.added_date, fz_users.login, fz_videos.status
                                     FROM fz_videos
                                     JOIN fz_users
                                     ON fz_videos.user_id=fz_users.id
                                     $tag_join_QUERY
                                     $search_by_event_JOIN
                                     WHERE fz_videos.user_id='$user_id' $search_QUERY
                                     ORDER BY '$order' '$order_dir'
                                     LIMIT $page_start, 11");
        }

        elseif($_GET["status"]=="liked" && isset($_SESSION["userid"]))
        {
          $status=$_GET["status"];
          $status_GET = "&status=liked";
          $result = $connect->query("SELECT fz_videos.id, fz_videos.name, fz_videos.added_date, fz_users.login, fz_videos.status
                                     FROM fz_videos
                                     JOIN fz_users
                                     ON fz_videos.user_id=fz_users.id
                                     $tag_join_QUERY
                                     $search_by_event_JOIN
                                     JOIN fz_videos_likes
                                     ON fz_videos_likes.user_id='$user_id'
                                     WHERE fz_videos_likes.user_id='$user_id' AND fz_videos.id=fz_videos_likes.performance_id $search_QUERY
                                     ORDER BY '$order' '$order_dir'
                                     LIMIT $page_start, 11");
        }
      }
      else
      {
        //domyślnie wszystkie
        $status="all";
        $status_GET="&status=all";
        $result = $connect->query("SELECT fz_videos.id, fz_videos.name, fz_videos.added_date, fz_users.login, fz_videos.status
                                   FROM fz_videos
                                   JOIN fz_users
                                   ON fz_videos.user_id=fz_users.id
                                   $tag_join_QUERY
                                   $search_by_event_JOIN
                                   $search_QUERY
                                   ORDER BY $order $order_dir
                                   LIMIT $page_start, 11");
      }
      ?>
      <!-- ustawienie selectów do odpowiednich wartości -->
      <script type="text/javascript">
        $(window).on('load', function()
        {
          document.getElementById("status").value = "<?php echo $status; ?>";
          document.getElementById("order").value = "<?php echo $order; ?>";
          document.getElementById("order_dir").value = "<?php echo $order_dir; ?>";
          document.getElementById("search_by_tag").value = "<?php echo $search_by_tag; ?>";
          document.getElementById("search_by_name").value = "<?php echo $search_by_name; ?>";
          document.getElementById("search_by_login").value = "<?php echo $search_by_login; ?>";
        });
      </script>
      <?php

      ?>
      <script type="text/javascript">
        function next_page()
        {
          location.href = '<?php echo "./videos-list.php?p=".($page+1).$status_GET.$order_GET.$order_dir_GET.$search_by_name_GET.$search_by_tag_GET.$search_by_event_GET; ?>';
        }
        function prev_page()
        {
          location.href = '<?php echo "./videos-list.php?p=".($page-1).$status_GET.$order_GET.$order_dir_GET.$search_by_name_GET.$search_by_tag_GET.$search_by_event_GET; ?>';
        }
      </script>
      <?php

      if($result!=null)
      {
        $video = array();
        $i=0;
        while($row = $result->fetch_assoc())
        {
          $video[$i][0]=$row["id"];
          $video[$i][1]=$row["name"];
          $video[$i][2]=$row["added_date"];
          $video[$i][3]=$row["login"];

          $temp=$connect->query("SELECT COUNT(id)
                                       FROM fz_videos_views
                                       WHERE performance_id=".$video[$i][0]);
          $temp=$temp->fetch_row();
          $video[$i][4]=$temp[0];

          $temp=$connect->query("SELECT COUNT(id)
                                       FROM fz_videos_likes
                                       WHERE performance_id=".$video[$i][0]);
          $temp=$temp->fetch_row();
          $video[$i][5]=$temp[0];
          $i++;
        }

        //filtrowanie wydarzeń
        ?>
        <div class="vl-container">
          <form class="form filter" method="get">
            <label class="vl-label">Tytuł</label>
              <input type="text" class="vl-input" name="search_by_name"  class="search-textbox" id="search_by_name">
            <label class="vl-label">Autor</label>
              <input type="text" class="vl-input" name="search_by_login" class="search-textbox" id="search_by_login">
            <label class="vl-label">Tag</label>
              <input type="text" class="vl-input" name="search_by_tag" class="search-textbox" id="search_by_tag"><br>
            <select name="status" class="search search-select" id="status">
              <option value="all">Wszystkie</option>
              <?php
              if(isset($_SESSION["userid"]))
              { ?>
                <option value="created">Utworzone przeze mnie</option>
                <option value="liked">Polubione</option>
              <?php } ?>
            </select>

            <select name="order" class="search-select" id="order">
              <option value="id">Kolejność utworzenia</option>
              <option value="name">Nazwa</option>
              <option value="login">Autor</option>
              <option value="added_date">Data utworzenia</option>
            </select>

            <select name="order_dir" class="search-select" id="order_dir">
              <option value="asc">Rosnąco</option>
              <option value="desc">Malejąco</option>
            </select>

            <input type=submit class="vl-submit" value="Filtruj">
          </form>

          <!-- wyswietlanie listy filmow - naglowej -->
          <table class="vl-table">
            <tr>
              <th>Tytuł</th>
              <th>Autor</th>
              <th>Data dodania</th>
              <th><img title="Wyświetlenia" src="./images/icons/eyeIcon_Black.png"></th>
              <th><img title="Polubienia" src="./images/icons/fireIconBlur_Black.png"></th>
            </tr>

          <?php
          if(isset($_GET["search_by_event"]) && $_GET["search_by_event"])
          {
            $temp_event = $_GET["search_by_event"]; ?>
            <a href="./videos-list.php?p=<?php echo $page.$status_GET.$order_GET.$order_dir_GET.$search_by_name_GET.$search_by_tag_GET; ?>"><button class="ep-submit">Odznacz wydarzenie</button class="vl-submit"></a>
            <a href="./event.php?v=<?php echo $temp_event; ?>"><button class="ep-submit">Wróć do wydarzenia</button class="vl-submit"></a></br>
        <?php  }
          ?>
          <?php
          //wyświetlanie wydarzeń
          if($video!=null)
          {
            if(count($video)>10)
            {
              $count_video=10;
            }
            else
            {
              $count_video=count($video);
            }
            //wyświetlanie hiperłączy do konkretnych wydarzeń
            for($j=0;$j<$count_video;$j++)
            {

              echo "<tr><td><a href=\"./play.php?v=".$video[$j][0]."\"> ".$video[$j][1]."</a></td>";
              echo "<td><a href=\"./profile.php?p=".$video[$j][3]."\"> ".$video[$j][3]."</a></td>";
              echo "<td>".$video[$j][2]."</td>";
              echo "<td>".$video[$j][4]."</td>";
              echo "<td>".$video[$j][5]."</td></tr>";
            }
          }
          else
          {
        ?>  <span>Nie ma więcej wykonań.</span> <?php
          }
        }
        else
        {
        ?>  <p>Błąd serwera</p> <?php
        }

        if($page<=1)
        {
          if(count($video)>10)
          {
          ?> <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
          }
        }
        elseif(count($video)>=10)
        { ?>
          <input type="button" class="vl-submit" value="Poprzednie" onclick="prev_page()">
          <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
        }
        else
        { ?>
          <input type="button" class="vl-submit" value="Poprzednie" onclick="prev_page()"> <?php
        }
      ?>
      </table>

      <a href="./upload-video.php"><button class="ep-submit">Dodaj wykonanie</button></a>

      <!-- menu mobilne -->
        <nav class="nav-bottom">
          <a href="./scripts/scriptLogout.php" class="nav-item">
          <i class='icon icon-logout fas fa-power-off'></i>
            <span class="menu-text">Wyloguj</span>
          </a>
          <a href="./edit-account.php" class="nav-item">
          <i class='icon fas fa-cog'></i>
            <span class="menu-text">Ustawienia</span>
          </a>
          <a href="./profile.php" class="nav-item">
          <i class='icon fas fa-user-alt'></i>
            <span class="menu-text">Mój profil</span>
          </a>
        <!-- <a href="./moderator.php"><i class='icon fas fa-tachometer-alt'></i></a> -->
      </nav>
    </div>
    <?php
    if(isset($_SESSION["video_error"]) && $_SESSION["video_error"])
    {
      ?><script> alert("<?php echo $_SESSION["video_error"]; ?>"); </script><?php
      unset($_SESSION["video_error"]);
    }
    ?>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
