<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Usuwanie konta - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Usuwanie konta</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    Czy na pewno chcesz zamknąć swoje konto?<br>
    <form class="ep_form" action="./scripts/scriptRemoveAccount.php" method="post">
      <?php if(isset($_SESSION["userid"]))
      { ?>
      <label>Podaj hasło:</label>
      <input type="password" name="ep_oldPassword"><br>
    <?php } ?>
      <input type="submit" class="submitDelete" value="Usuń"/>
    </form>
    <input type="submit" class="submitDelete" value="Nie usuwaj" onclick="go_back()"/>

    <script>
      function remove_event()
      {
        window.location='./scripts/scriptRemoveAccount.php';
      }
    </script>
    <script>
      function go_back()
      {
        window.location='./edit-account.php';
      }
    </script>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
