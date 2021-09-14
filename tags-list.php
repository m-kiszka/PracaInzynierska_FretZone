<?php session_start(); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_Moderator.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <meta charset="utf-8">
    <title>Lista tagów - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Lista tagów</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <?php

      $count_tag=0;

      // if(isset($_SESSION["event_error"]) && $_SESSION["event_error"])
      // {
      //   echo $_SESSION["event_error"];
      //   unset($_SESSION["event_error"]);
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
          $search_by_name_QUERY="AND fz_tags.name LIKE '%".$_GET["search_by_name"]."%'";
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
        $temp_result = $connect->query("SELECT user_rank FROM fz_users WHERE id='$user_id'");
        $temp_result = $temp_result->fetch_row();
        if(!isset($temp_result) || $temp_result<3)
        {
          echo "<script>location.href=\"./index.php\";</script>";
          die();
        }
      }
      else
      {
        echo "<script>location.href=\"./index.php\";</script>";
        die();
      }

      $page_start = 10*$page-10;

      $result=null;

      if($search_by_name!="")
      {
        $result = $connect->query("SELECT id, name
                                   FROM fz_tags
                                   WHERE $search_by_name_QUERY
                                   ORDER BY name $order_dir
                                   LIMIT $page_start, 11");
      }
      else
      {
        $result = $connect->query("SELECT id, name
                                   FROM fz_tags
                                   ORDER BY name $order_dir
                                   LIMIT $page_start, 11");
      }
      ?>
      <!-- ustawienie selectów do odpowiednich wartości -->
      <script type="text/javascript">
        $(window).on('load', function()
        {
          document.getElementById("order_dir").value = "<?php echo $order_dir; ?>";
          document.getElementById("search_by_name").value = "<?php echo $search_by_name; ?>";
        });
      </script>

      <script type="text/javascript">
        function next_page()
        {
          location.href = '<?php echo "./tags-list.php?p=".($page+1).$order_dir_GET.$search_by_name_GET; ?>';
        }
        function prev_page()
        {
          location.href = '<?php echo "./tags-list.php?p=".($page-1).$order_dir_GET.$search_by_name_GET; ?>';
        }
      </script>
      <?php

      if($result!=null)
      {
        $tag = array();
        $i=0;
        while($row = $result->fetch_assoc())
        {
          $tag[$i][0]=$row["id"];
          $tag[$i][1]=$row["name"];
          $i++;
        }

        //filtrowanie wydarzeń
        ?>
        <div class="tag-container">
          <form class="form" method="get">
            <label>Tag</label>
            <input type="text" name="search_by_name" class="tag-input search-textbox" id="search_by_name">

            <label>Sortowanie</label>
            <select name="order_dir" class="search-select" id="order_dir">
              <option value="asc">Rosnąco</option>
              <option value="desc">Malejąco</option>
            </select><br>

            <input type=submit class="vl-submit" value="Filtruj">
          </form>

            <table class="tags-table">
              <tr>
                <th>Nazwa</th>
                <th>Opcje</th>
              </tr>

              <?php
              //wyświetlanie wydarzeń
              if($tag!=null)
              {
                if(count($tag)>10)
                {
                  $count_tag=10;
                }
                else
                {
                  $count_tag=count($tag);
                }
                //wyświetlanie hiperłączy do konkretnych wydarzeń
                for($j=0;$j<$count_tag;$j++)
                {
                  // [0] - id(tylko do linku)
                  // [1] - nazwa wydarzenia
                  ?>
                  <!-- wiersz kolumnowy --> <tr><td class="name-tag"> <?php echo $tag[$j][1]; ?></td>
                  <td><a href="./scripts/scriptRemoveTag.php?id=<?php echo $tag[$j][0]; ?>"><button class="tag-button">Usuń</button></a></td><br>
                  <?php
                }
              }
              else
              {
                ?> <span>Nie ma więcej tagów.</span> <?php
              }
            }
            else
            {
            ?><span>Błąd serwera.</span> <?php
            }
            ?>
          </table>
          <?php
          if($page<=1)
          {
            if(count($tag)>10)
            {
            ?> <center><br><input class="vl-submit" type="button" value="Następne" onclick="next_page()"></center> <?php
            }
          }
          elseif(count($tag)>=10)
          { ?>
            <center><br><input class="vl-submit" type="button" value="Poprzednie" onclick="prev_page()"></center>
            <center><br><input class="vl-submit" type="button" value="Następne" onclick="next_page()"></center> <?php
          }
          else
          { ?>
            <center><br><input class="vl-submit" type="button" value="Poprzednie" onclick="prev_page()"></center> <?php
          } ?>
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
    if(isset($_SESSION["event_error"]) && $_SESSION["event_error"]) {
       ?><script>alert("<?php echo $_SESSION["event_error"];?>")</script><?php
       unset($_SESSION["event_error"]);
     } ?>
     <footer>
      <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
     </footer>
  </body>
</html>
