<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Usuwanie wydarzenia - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
  </head>
  <body>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Usuwanie wydarzenia</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <div class="ul-container">
      <div class="info">
    <?php
    include('./scripts/scriptCookiesBanner.html');

    if(!isset($_SESSION["event_id"]))
    {
      echo "<script>window.location='./not-found.php'</script>;";
      die();
    }
    else
    {
      $event_id = $_SESSION["event_id"];
      $event_name = $_SESSION["event_name"];
      $_SESSION["event_change"]=1;
      unset($_SESSION["event_name"]);
      unset($_SESSION["event_desc"]);
      unset($_SESSION["event_tab"]);
    } ?>

    <br><h3>Czy na pewno chcesz usunąć to wydarzenie?</h3>
    <p><?php echo $event_name ?></p>
    <input type="submit" class="submitDelete" value="Usuń" onclick="remove_event()"/>
    <input type="submit" class="submitDelete submitNo" value="Nie usuwaj" onclick="go_back()"/>

    </div>
  </div>
    <script>
      function remove_event()
      {
        window.location='./scripts/scriptRemoveEvent.php';
      }
    </script>
    <script>
      function go_back()
      {
        window.location='./event.php?v=<?php echo $event_id ?>';
      }
    </script>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
