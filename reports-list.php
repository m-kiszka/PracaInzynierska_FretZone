<?php session_start(); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_Moderator.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <link rel="stylesheet" href="./css/style2.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <meta charset="utf-8">
    <title>Lista zgłoszeń - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Lista zgłoszeń</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <!-- <div class="error">
      <?php /* if(isset($_SESSION["report_error"]) && $_SESSION["report_error"]!="")
              {  echo $_SESSION["report_error"]; unset($_SESSION["report_error"]);
              }
      */ ?>
    </div> -->

    <?php

    $temp_id = $_SESSION["userid"];
    $login = $connect->query("SELECT login FROM fz_users WHERE id='$temp_id'");
    $login = $login->fetch_row();

      $count_report=0;

    /*  if(isset($_SESSION["event_error"]) && $_SESSION["event_error"])
      {
        echo $_SESSION["event_error"];
        unset($_SESSION["event_error"]);
      } */

      //odczytywanie na której stronie użytkownik jest
      if(isset($_GET["p"]) && $_GET["p"]!="" && $_GET["p"]>0)
      {
        $page = $_GET["p"];
      }
      else
      {
        $page=1;
      }

      //po czym sortować
      if(isset($_GET["order"]) && $_GET["order"]!="")
      {
        $order = $_GET["order"];
        $order_GET="&order=".$order;

        if($order!="id" && $order!="report_type" && $order!="added_date" && $order!="status")
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

      //jakie rekordy wyświetlać
      if(isset($_GET["restrict"]) && $_GET["restrict"]!="")
      {
        $restrict = $_GET["restrict"];
        if($restrict!="open" || $restrict!="closed" || $restrict!="in-progress" || $restrict!="")
        {
          $restrict = htmlspecialchars($restrict);
          $restrict_QUERY = "WHERE status='".$restrict."'";
        }
        if($restrict=="open")
        {
            $restrict_QUERY = "WHERE status='Open'";
        }
        if($restrict=="closed")
        {
            $restrict_QUERY = "WHERE status='Closed'";
        }
        if($restrict=="in-progress")
        {
            $restrict_QUERY = "WHERE NOT status='Closed' AND NOT status='Open'";
        }
        if($restrict=="")
        {
            $restrict_QUERY = "WHERE 1=1";
        }
        $restrict_GET="&restrict=".$restrict;
      }
      else
      {
        $restrict_QUERY="WHERE 1=1";
        $restrict_GET="&restrict=";
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

      $page_start = 10*$page-10;

      $result=null;

      $result = $connect->query("SELECT id, report_type, description, added_date, user_id, type, url, status
                                   FROM fz_reports
                                   $restrict_QUERY
                                   ORDER BY $order $order_dir
                                   LIMIT $page_start, 11");
      ?>
      <!-- ustawienie selectów do odpowiednich wartości -->
      <script type="text/javascript">
        $(window).on('load', function()
        {
          document.getElementById("order_dir").value = "<?php echo $order_dir; ?>";
          document.getElementById("order").value = "<?php echo $order; ?>";
          document.getElementById("restrict").value = "<?php echo $restrict; ?>";
        });
      </script>

      <script type="text/javascript">
        function next_page()
        {
          location.href = '<?php echo "./reports-list.php?p=".($page+1).$order_GET.$order_dir_GET.$restrict_GET; ?>';
        }
        function prev_page()
        {
          location.href = '<?php echo "./reports-list.php?p=".($page-1).$order_GET.$order_dir_GET.$restrict_GET; ?>';
        }
      </script>
      <?php
      if($result!=null)
      {
        $report = array();
        $i=0;
        while($row = $result->fetch_assoc())
        {
          $report[$i][0]=$row["id"];
          $report[$i][1]=$row["report_type"];
          $report[$i][2]=$row["added_date"];
          $report[$i][3]=$row["type"];
          $report[$i][4]=$row["status"];
          $i++;
        }

        //filtrowanie wydarzeń
        ?>
        <div class="ev-container rl-container">
          <center><form class="filter" method="get">
            <select name="order" class="search-select" id="order">
              <option value="id">Kolejność utworzenia</option>
              <option value="report_type">Login</option>
              <option value="added_date">Data zgłoszenia</option>
              <option value="status">Status</option>
            </select>

            <select name="order_dir" class="search-select" id="order_dir">
              <option value="asc">Rosnąco</option>
              <option value="desc">Malejąco</option>
            </select>

            <select name="restrict" class="search-select" id="restrict">
              <option value="">Wszystkie</option>
              <option value="open">Otwarte</option>
              <option value="in-progress">W trakcie</option>
              <option value="closed">Zamknięte</option>
              <option value="<?php echo $login[0];?>">Moje</option>
            </select>

            <br><input type=submit class="vl-submit" value="Filtruj">
          </form></center>
          <table style="border: none; margin-top: 50px;">
            <tr>
              <th>Status</th>
              <th>Rodzaj zgłoszenia</th>
              <th>Data zgłoszenia<th>
              <th></th>
            </tr>
            <tr>

          <?php
          //wyświetlanie zgłoszeń
          if($report!=null)
          {
            if(count($report)>10)
            {
              $count_report=10;
            }
            else
            {
              $count_report=count($report);
            }

            for($j=0;$j<$count_report;$j++)
            {
              ?>
                <tr>
                    <?php
                      if($report[$j][4]=="Open")
                      {
                        // kategoria
                        ?><td>Otwarte</td><?php
                      }
                      elseif($report[$j][4]=="Closed")
                      {
                        ?><td>Zamknięte</td><?php
                      }

                      else
                      {
                        ?><td><a href="./profile.php?p=<?php echo $report[$j][4]; ?>"><?php echo $report[$j][4]; ?></a></td><?php
                      }
                    ?>


              <td><?php echo $report[$j][1]; ?></td>
              <td><?php echo $report[$j][2]; ?></td>
              <?php
              if($report[$j][4]=="Open")
              { ?>
                <td><a href="./report.php?id=<?php echo $report[$j][0]; ?>"><button>Przyjmij zgłoszenie</button></a><br></td>
        <?php }
              else
              { ?>
              <td><a href="./report.php?id=<?php echo $report[$j][0]; ?>"><button>Zobacz zgłoszenie</button></a><br></td>
        <?php }
            }
          }
          else
          {
            ?> <span>Nie ma więcej zgłoszeń.</span> <?php
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
          if(count($report)>10)
          {
          ?> <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
          }
        }
        elseif(count($report)>=10)
        { ?>
          <input type="button" class="vl-submit" value="Poprzednie" onclick="prev_page()">
          <input type="button" class="vl-submit" value="Następne" onclick="next_page()"> <?php
        }
        else
        { ?>
          <input type="button" class="vl-submit" value="Poprzednie" onclick="prev_page()"> <?php
        } ?>

    </div>

    <!-- Komunikat o błędzie -->
    <?php
    if(isset($_SESSION["report_error"]) && $_SESSION["report_error"]!="") {
       ?><script>alert("<?php echo $_SESSION["report_error"];?>")</script><?php
       unset($_SESSION["report_error"]);
     }

    if(isset($_SESSION["event_error"]) && $_SESSION["event_error"]) {
      ?><script>alert("<?php echo $_SESSION["event_error"];?>")</script><?php
      unset($_SESSION["event_error"]);
      }
    ?>

    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
