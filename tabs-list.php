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
    <title>Lista tabulatur - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Lista tabulatur</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <?php
      $count_tab=0;

      // if(isset($_SESSION["tabs_error"]) && $_SESSION["tabs_error"])
      // {
      //   echo $_SESSION["tabs_error"];
      //   unset($_SESSION["tabs_error"]);
      // }

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
          $search_by_name_QUERY="AND fz_tabs.name LIKE '%".$_GET["search_by_name"]."%'";
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
          $tag_join_QUERY="JOIN fz_tabs_tags ON fz_tabs.id = fz_tabs_tags.tab_id JOIN fz_tags ON fz_tabs_tags.tag_id=fz_tags.id";
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

        if($order!="id" && $order!="name")
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

      $result = $connect->query("SELECT fz_tabs.id, fz_tabs.name, fz_tabs.added_date, fz_users.login
                                 FROM fz_tabs
                                 JOIN fz_users
                                 ON fz_tabs.user_id=fz_users.id
                                 $tag_join_QUERY
                                 WHERE fz_tabs.status!='LOCKED' $search_by_name_QUERY $search_by_login_QUERY $search_by_tag_QUERY
                                 ORDER BY $order $order_dir
                                 LIMIT $page_start, 11");
      ?>
      <!-- ustawienie selectów do odpowiednich wartości -->
      <script type="text/javascript">
        $(window).on('load', function()
        {
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
          location.href = '<?php echo "./tabs-list.php?p=".($page+1).$status_GET.$order_GET.$order_dir_GET.$search_by_name_GET.$search_by_login_GET.$search_by_tag_GET; ?>';
        }
        function prev_page()
        {
          location.href = '<?php echo "./tabs-list.php?p=".($page-1).$status_GET.$order_GET.$order_dir_GET.$search_by_name_GET.$search_by_login_GET.$search_by_tag_GET; ?>';
        }
      </script>
      <?php

      if($result!=null)
      {
        $tab = array();
        $i=0;
        while($row = $result->fetch_assoc())
        {
          $tab[$i][0]=$row["id"];
          $tab[$i][1]=$row["name"];
          $tab[$i][2]=$row["login"];
          $tab[$i][3]=$row["added_date"];
          $i++;
        }

        //filtrowanie wydarzeń
        ?>
        <div class="el-container">
          <form class="filter" method="get">
            <span class="el-span">Tabulatura</span>
              <input type="text" name="search_by_name" class="search-textbox" id="search_by_name">
            <span class="el-span">Autor</span>
              <input type="text" name="search_by_login" class="search-textbox" id="search_by_login">
            <span class="el-span">Tag</span>
              <input type="text" name="search_by_tag" class="search-textbox" id="search_by_tag"><br>

          <span>Sortowanie: </span>
            <select name="order" class="search-select" id="order">
              <option value="id">Kolejność utworzenia</option>
              <option value="name">Nazwa</option>
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
               <th>Tabulatura</th>
               <th>Autor</th>
               <th>Data utworzenia</th>
             </tr>
           <!-- koniec wiersza naglowkowego -->

        <?php
        //wyświetlanie wydarzeń
        if($tab!=null)
        {
          if(count($tab)>10)
          {
            $count_tab=10;
          }
          else
          {
            $count_tab=count($tab);
          }
          //wyświetlanie hiperłączy do konkretnych wydarzeń
          for($j=0;$j<$count_tab;$j++)
          {
            // [0] - id(tylko do linku)
            // [1] - nazwa wydarzenia
            // [2] -
            // [3]
            // [4] - autor
            ?>

            <!-- wiersz kolumnowy -->
            <?php echo "<tr><td><a href=\"./tab.php?v=".$tab[$j][0]."\"> ".$tab[$j][1]."</a></td>";
            echo "<td><a href=\"./profile.php?p=".$tab[$j][2]."\">".$tab[$j][2]."</td>";
            echo "<td>".$tab[$j][3]."</td>";
            echo "</tr><br>";
          }
        }
        else
        {
          ?> <br><span>Nie ma więcej tabulatur.</span> <?php
        }
      }
      else
      {
      ?><span>Błąd serwera.</span><?php
      }

      if($page<=1)
      {
        if(count($tab)>10)
        {
        ?> <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
        }
      }
      elseif(count($tab)>=10)
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

      <a href="./add-tabs.php"><button class="ep-submit">Dodaj tabulaturę</button></a>

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
    <!-- Komunikat o błędzie -->
    <?php
    if(isset($_SESSION["tabs_error"]) && $_SESSION["tabs_error"]) {
       ?><script>alert("<?php echo $_SESSION["tabs_error"];?>")</script><?php
       unset($_SESSION["tabs_error"]);
     } ?>
     <footer>
    <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
   </footer>
  </body>
</html>
