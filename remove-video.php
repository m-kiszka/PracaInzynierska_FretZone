<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Usuwanie wykonania - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
  </head>
  <body>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Usuwanie wykonania</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <?php
    include('./scripts/scriptCookiesBanner.html');
    if(!isset($_SESSION["perf_id"]) || !isset($_SESSION["perf_name"]))
    {
      echo "<script>window.location='./not-found.php'</script>;";
      die();
    }
    else
    {
      $perf_id = $_SESSION["perf_id"];
      $perf_name = $_SESSION["perf_name"];
      $_SESSION["perf_change"]=1;
      unset($_SESSION["perf_name"]);
      unset($_SESSION["perf_desc"]);
      unset($_SESSION["perf_type"]);
      unset($_SESSION["perf_url"]);
    }
    ?>
    <div class="ul-container">
      <div class="info" style="text-align: center !important;">
        <h2>Czy na pewno chcesz usunąć to wykonanie?</h2>
        <h3><?php echo $perf_name; ?></h3>
        <input type="submit" class="submitDelete" value="Usuń" onclick="remove_event()"/>
        <input type="submit" class="submitDelete submitNo" value="Nie usuwaj" onclick="go_back()"/>
      </div>
    </div>

      <script>
        function remove_event()
        {
          window.location='./scripts/scriptRemoveVideo.php';
        }
      </script>
      <script>
        function go_back()
        {
          window.location='./play.php?v=<?php echo $perf_id ?>';
        }
      </script>
      <footer>
       <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
      </footer>
  </body>
</html>
