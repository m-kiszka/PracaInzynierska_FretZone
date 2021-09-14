<?php session_start(); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_Moderator.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <meta charset="utf-8">
    <title>Lista użytkowników - FretZone</title>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
  </head>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Lista użytkowników</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <?php

      $count_user=0;

      if(isset($_SESSION["edit_user_error"]) && $_SESSION["edit_user_error"])
      {
        echo $_SESSION["edit_user_error"];
        unset($_SESSION["edit_user_error"]);
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
          $search_by_name_QUERY="AND fz_users.login LIKE '%".$_GET["search_by_name"]."%'";
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

      //po czym sortować
      if(isset($_GET["order"]) && $_GET["order"]!="")
      {
        $order = $_GET["order"];
        $order_GET="&order=".$order;

        if($order!="id" && $order!="login" && $order!="account_creation_date" && $order!="user_rank" && $order!="oauth")
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

      /* czy użytkownik jest zalogowany */
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
        $result = $connect->query("SELECT id, login, account_creation_date, oauth, user_rank
                                   FROM fz_users
                                   WHERE $search_by_name_QUERY
                                   ORDER BY $order $order_dir
                                   LIMIT $page_start, 11");
      }
      else
      {
        $result = $connect->query("SELECT id, login, account_creation_date, oauth, user_rank
                                   FROM fz_users
                                   ORDER BY $order $order_dir
                                   LIMIT $page_start, 11");
      }
      ?>
      <!-- ustawienie selectów do odpowiednich wartości -->
      <script type="text/javascript">
        $(window).on('load', function()
        {
          document.getElementById("order_dir").value = "<?php echo $order_dir; ?>";
          document.getElementById("order").value = "<?php echo $order; ?>";
          document.getElementById("search_by_name").value = "<?php echo $search_by_name; ?>";
        });
      </script>

      <script type="text/javascript">
        function next_page()
        {
          location.href = '<?php echo "./users-list.php?p=".($page+1).$order_GET.$order_dir_GET.$search_by_name_GET; ?>';
        }
        function prev_page()
        {
          location.href = '<?php echo "./users-list.php?p=".($page-1).$order_GET.$order_dir_GET.$search_by_name_GET; ?>';
        }
      </script>
      <?php

      if($result!=null)
      {
        $user = array();
        $i=0;
        while($row = $result->fetch_assoc())
        {
          $user[$i][0]=$row["id"];
          $user[$i][1]=$row["login"];
          $user[$i][2]=$row["account_creation_date"];
          $user[$i][3]=$row["oauth"];
          $user[$i][4]=$row["user_rank"];
          switch($user[$i][4])
          {
            case -1:
              $user[$i][4]="Zawieszony";
            break;
            case 0:
              $user[$i][4]="Niezweryfikowany";
            break;
            case 1:
              $user[$i][4]="Użytkownik";
            break;
            case 2:
              $user[$i][4]="VIP";
            break;
            case 3:
              $user[$i][4]="Moderator";
            break;
            case 4:
              $user[$i][4]="Administrator";
            break;
          }
          $i++;
        }

        //filtrowanie wydarzeń
        ?>
        <div class="ul-container">
          <form class="filter" method="get">
            <span>Nazwa użytkownika</span>
            <input type="text" name="search_by_name" class="vl-input search-textbox" id="search_by_name">

            <select name="order" class="search search-select" id="order">
              <option value="id">Kolejność utworzenia</option>
              <option value="login">Nazwa użytkownika</option>
              <option value="account_creation_date">Data utworzenia konta</option>
              <option value="oauth">OAuth</option>
              <option value="user_rank">Poziom uprawnień</option>
            </select>

            <select name="order_dir" class="search-select" id="order_dir">
              <option value="asc">Rosnąco</option>
              <option value="desc">Malejąco</option>
            </select>

            <input type=submit class="vl-submit" value="Filtruj">
          </form>
          <!-- tabela -->
          <table class="vl-table">
            <tr>
              <th>Nazwa użytkownika</th>
              <th>Data utworzenia konta</th>
              <th>Poziom uprawnień</th>
            </tr>
          <?php
          //wyświetlanie wydarzeń
          if($user!=null)
          {
            if(count($user)>10)
            {
              $count_user=10;
            }
            else
            {
              $count_user=count($user);
            }
            //wyświetlanie hiperłączy do konkretnych wydarzeń
            for($j=0;$j<$count_user;$j++)
            {
              // [0] - id(tylko do linku)
              // [1] - nazwa wydarzenia
              ?>

              <!-- wiersz kolumnowy -->
              <tr>
                <td><a href="./profile.php?p=<?php echo $user[$j][1]; ?>"><span class="name-user"> <?php echo $user[$j][1]; ?></a></span></td>
                <td><span class="date-user"> <?php echo $user[$j][2]; ?></span></td>
                <td><span class="rank-user"><?php echo $user[$j][4]; ?></span></td>

                <?php if($user[$j][4]>$rank_result[0] && $rank_result[0]>=3)
                { ?>
                <td><a href=./edit-user.php?p=<?php echo $user[$j][1]; ?>><button>Edytuj użytkownika</button></a></td>
            <?php }
            }
          }
          else
          {
            ?> <td><span>Nie ma więcej użytkowników.</span></td> <?php
          }
        }
        else
        {
        ?><td><span>Błąd serwera.</span></td> <?php
        }
        ?> </tr></table><br> <?php

        if($page<=1)
        {
          if(count($user)>10)
          {
          ?> <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
          }
        }
        elseif(count($user)>=10)
        { ?>
          <input type="button" class="vl-submit" value="Poprzednie" onclick="prev_page()">
          <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
        }
        else
        { ?>
          <input type="button" class="vl-submit" value="Poprzednie" onclick="prev_page()"> <?php
        } ?>

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
          </nav>
      </div>
      <?php
        if(isset($_SESSION["edit_user_error"]) && $_SESSION["edit_user_error"])
        {
        ?> <script> alert("<?php echo $_SESSION["edit_user_error"]; ?>");</script> <?php
          unset($_SESSION["edit_user_error"]);
        }
      ?>
      <footer>
       <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
      </footer>
  </body>
</html>
