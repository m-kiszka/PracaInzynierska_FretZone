<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <meta charset="utf-8">
    <title>Lista wydarzeń - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Lista wydarzeń</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <?php
      $count_event=0;

      //odczytywanie na której stronie użytkownik jest
      if(isset($_GET["p"]) && $_GET["p"]!="" && $_GET["p"]>0)
      {
        $page = $_GET["p"];
      }
      else
      {
        $page=1;
      }

      //zapytanie od nazwy
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
          $search_by_name_QUERY="AND fz_chall.name LIKE '%".$_GET["search_by_name"]."%'";
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
          $search_by_login_QUERY="AND fz_users.login LIKE '%".$_GET["search_by_login"]."%'";
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

      //zapytanie od tagów
      if(isset($_GET["search_by_tag"]) && $_GET["search_by_tag"]!="")
      {
        if(!preg_match("/^[A-Za-z0-9,-]+$/", $_GET["search_by_tag"]))
        {
          $tag_join_QUERY="";
          $search_by_tag_QUERY="";
          $search_by_tag="";
          $search_by_name_GET="";
        }
        else
        {
          $search_by_tag=$_GET["search_by_tag"];
          $tag_join_QUERY="JOIN fz_chall_tags ON fz_chall.id = fz_chall_tags.chall_id JOIN fz_tags ON fz_chall_tags.tag_id=fz_tags.id";
          $search_by_tag_QUERY=" AND fz_tags.name LIKE '%".$_GET["search_by_tag"]."%'";
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

      //po czym sortować
      if(isset($_GET["order"]) && $_GET["order"]!="")
      {
        $order = $_GET["order"];
        $order_GET="&order=".$order;

        if($order!="id" && $order!="name" && $order!="start_date" && $order!="end_date")
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
      if(isset($_SESSION["userid"]) && isset($_SESSION["login"]))
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
        //nadchodzące
        if($_GET["status"]=="upcoming")
        {
          $status=$_GET["status"];
          $status_GET = "&status=upcoming";
          $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.status
                                     FROM fz_chall
                                     JOIN fz_users
                                     ON fz_chall.user_id=fz_users.id
                                     $tag_join_QUERY
                                     WHERE fz_chall.status!='LOCKED' AND CURRENT_DATE()<start_date $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
                                     ORDER BY $order $order_dir
                                     LIMIT $page_start, 11");
        }
        //zakończone
        elseif($_GET["status"]=="ended")
        {
          $status=$_GET["status"];
          $status_GET = "&status=ended";
          $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.status
                                     FROM fz_chall
                                     JOIN fz_users
                                     ON fz_chall.user_id=fz_users.id
                                     $tag_join_QUERY
                                     WHERE fz_chall.status!='LOCKED' AND CURRENT_DATE()>end_date $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
                                     ORDER BY $order $order_dir
                                     LIMIT $page_start, 11");
        }
        //brano udział
        elseif($_GET["status"]=="participated" && isset($user_id))
        {
          $status_GET = "&status=participated";
          $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.status
                                     FROM fz_chall
                                     JOIN fz_videos
                                     ON fz_chall.id=fz_videos.chall_id
                                     JOIN fz_users
                                     ON fz_chall.user_id=fz_users.id
                                     $tag_join_QUERY
                                     WHERE fz_chall.status!='LOCKED' AND fz_videos.user_id='$user_id' $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
                                     ORDER BY '$order' '$order_dir'
                                     LIMIT $page_start, 11");
        }
        //stworzono
        elseif($_GET["status"]=="created" && isset($user_id))
        {
          $status=$_GET["status"];
          $status_GET = "&status=created";
          $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.status
                                     FROM fz_chall
                                     JOIN fz_users
                                     ON fz_chall.user_id=fz_users.id
                                     $tag_join_QUERY
                                     WHERE fz_chall.status!='LOCKED' AND fz_chall.user_id='$user_id' $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
                                     ORDER BY '$order' '$order_dir'
                                     LIMIT $page_start, 11");
        }
        //aktywne
        elseif($_GET["status"]=="active")
        {
          $status="active";
          $status_GET="&status=active";
          $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.status
                                     FROM fz_chall
                                     JOIN fz_users
                                     ON fz_chall.user_id=fz_users.id
                                     $tag_join_QUERY
                                     WHERE fz_chall.status!='LOCKED' AND CURRENT_DATE() between start_date and end_date $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
                                     ORDER BY $order $order_dir
                                     LIMIT $page_start, 11");
        }
        else
        {
          $status="all";
          $status_GET="&status=all";
          $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.status
                                     FROM fz_chall
                                     JOIN fz_users
                                     ON fz_chall.user_id=fz_users.id
                                     $tag_join_QUERY
                                     WHERE fz_chall.status!='LOCKED' $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
                                     ORDER BY $order $order_dir
                                     LIMIT $page_start, 11");
        }
      }
      else
      {
        $status="active";
        $status_GET="&status=active";
        $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.status
                                   FROM fz_chall
                                   JOIN fz_users
                                   ON fz_chall.user_id=fz_users.id
                                   $tag_join_QUERY
                                   WHERE fz_chall.status!='LOCKED' AND CURRENT_DATE() between start_date and end_date $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
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
          location.href = '<?php echo "./events-list.php?p=".($page+1).$status_GET.$order_GET.$order_dir_GET.$search_by_name_GET.$search_by_login_GET.$search_by_tag_GET; ?>';
        }
        function prev_page()
        {
          location.href = '<?php echo "./events-list.php?p=".($page-1).$status_GET.$order_GET.$order_dir_GET.$search_by_name_GET.$search_by_login_GET.$search_by_tag_GET; ?>';
        }
      </script>
      <?php

      if($result!=null)
      {
        $event = array();
        $i=0;
        while($row = $result->fetch_assoc())
        {
          $event[$i][0]=$row["id"];
          $event[$i][1]=$row["name"];
          $event[$i][2]=$row["start_date"];
          $event[$i][3]=$row["end_date"];
          $event[$i][4]=$row["login"];
          $i++;
        }

        //filtrowanie wydarzeń
        ?>
        <div class="el-container">
          <!-- <div class="error">
            <?php
                /* if(isset($_SESSION["upload_error"]))
                {
                  echo $_SESSION["upload_error"];
                  unset($_SESSION["upload_error"]);
                } */
            ?>
          </div> -->
          <form class="filter" method="get">
            <span class="el-span">Wydarzenie</span>
              <input type="text" name="search_by_name" class="search-textbox" id="search_by_name">
            <span class="el-span">Autor</span>
              <input type="text" name="search_by_login" class="search-textbox" id="search_by_login">
            <span class="el-span">Tag</span>
              <input type="text" name="search_by_tag" class="search-textbox" id="search_by_tag"><br>
            <label style="margin-left: 8px;">Sortowanie:</label>
            <select name="status" class="search search-select" id="status">
              <option value="all">Wszystkie</option>
              <option value="active">Trwające</option>
              <option value="upcoming">Nadchodzące</option>
              <option value="ended">Zakończone</option>
              <?php if(isset($_SESSION["userid"]))
              { ?>
                <option value="created">Utworzone przeze mnie</option>
              <?php } ?>
            </select>

            <select name="order" class="search-select" id="order">
              <option value="id">Kolejność utworzenia</option>
              <option value="name">Nazwa</option>
              <option value="start_date">Data rozpoczęcia</option>
              <option value="end_date">Data zakończenia</option>
            </select>

            <select name="order_dir" class="search-select" id="order_dir">
              <option value="asc">Rosnąco</option>
              <option value="desc">Malejąco</option>
            </select>

            <input type=submit class="vl-submit" value="Filtruj">
          </form>

          <!-- wiersz naglowkowy -->
           <table>
             <tr>
               <th>Wydarzenie</th>
               <th>Autor</th>
               <th>Data rozpoczęcia</th>
               <th>Data zakończenia</th>
             </tr>
           <!-- koniec wiersza naglowkowego -->

        <?php
        //wyświetlanie wydarzeń
        if($event!=null)
        {
          if(count($event)>10)
          {
            $count_event=10;
          }
          else
          {
            $count_event=count($event);
          }
          //wyświetlanie hiperłączy do konkretnych wydarzeń
          for($j=0;$j<$count_event;$j++)
          {

            /* wiersz kolumnowy */
            echo "<tr><td><a href=\"./event.php?v=".$event[$j][0]."\"> ".$event[$j][1]."</a></td>";
            echo "<td><a href=\"./profile.php?p=".$event[$j][4]."\"> ".$event[$j][4]."</td>";
            echo "<td>".$event[$j][2]."</td>";
            echo "<td>".$event[$j][3]."</td>";
            echo "</tr><br>";
          }
        }
        else
        {
          ?> <span></br>Nie ma więcej wydarzeń.</span> <?php
        }
      }
      else
      {
        ?> <span>Błąd serwera.</span> <?php
      }

      if($page<=1)
      {
        if(count($event)>10)
        {
        ?> <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
        }
      }
      elseif(count($event)>=10)
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

      <a href="./add-event.php"><button class="ep-submit">Dodaj wydarzenie</button></a>

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
        </div>
    </div>

    <!-- Komunikat o błędzie -->
    <?php
    if(isset($_SESSION["upload_error"]) && $_SESSION["upload_error"]!="") {
       ?><script>alert("<?php echo $_SESSION["upload_error"];?>")</script><?php
       unset($_SESSION["upload_error"]);
     } ?>

     <?php
     if(isset($_SESSION["event_error"]) && $_SESSION["event_error"]!="") {
        ?><script>alert("<?php echo $_SESSION["event_error"];?>")</script><?php
        unset($_SESSION["event_error"]);
      } ?>
  </body>
  <footer>
   <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
  </footer>
</html>
