<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Usuwanie tabulatury - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
  </head>
  <body>
    <?php
    include('./scripts/scriptCookiesBanner.html');
    if(!isset($_SESSION["tab_id"]))
    {
      echo "<script>window.location='./not-found.php'</script>;";
      die();
    }
    else
    {
      $tab_id = $_SESSION["tab_id"];
      $tab_name = $_SESSION["tab_name"];
      $_SESSION["tab_change"]=1;
      unset($_SESSION["tab_name"]);
      unset($_SESSION["tab_desc"]);
      unset($_SESSION["tab_tab"]);
    }
?>

    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Usuwanie tabulatury</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

<div class="ul-container">
  <div class="info">
    Czy na pewno chcesz usunąć tę tabulaturę?<br>
    <p><?php echo $tab_name; ?></p>
    <input type="submit" class="submitDelete" value="Usuń" onclick="remove_tab()"/>
    <input type="submit" class="submitDelete submitNo" value="Nie usuwaj" onclick="go_back()"/>
  </div>
</div>
    <script>
      function remove_tab()
      {
        window.location='./scripts/scriptRemoveTab.php';
      }
    </script>
    <script>
      function go_back()
      {
        window.location='./tab.php?v=<?php echo $tab_id ?>';
      }
    </script>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
