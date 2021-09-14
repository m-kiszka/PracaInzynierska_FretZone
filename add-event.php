<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <!--  rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <title>Dodaj wydarzenie - FretZone</title>
  </head>
  <body>
        <?php include('./scripts/scriptCookiesBanner.html');?>
        <div class="banner">
          <a title="Strona główna" href="./index.php">
            <div class="logo"></div>
          </a>
            <p class="logo-text">Dodaj wydarzenie</p>
          <?php include('./scripts/scriptBannerButtons.php'); ?>
        </div>
    <div class="ae-container">
      <!-- <div class="error">
          <?php
            /* if(isset($_SESSION["add_event_error"]))
            {
              echo "Błąd formularza: ".$_SESSION["add_event_error"];
              unset($_SESSION["add_event_error"]);
            } */
          ?>
      </div> -->
      <form class="ae-form" action="./scripts/scriptAddEvent.php" method="post">
        <label>Nazwa wydarzenia* </label><br>
          <input type="text" class="ae-input" name="ae_title" placeholder="np. Gramy piosenki kojarzące się z morzem"><br>
        <label>Opis (obsługuje formatowanie <a href="./formatting-guide.php" target="_blank">BBCode</a>)* </label><br>
          <textarea class="ae-textarea" name="ae_description" rows="8" cols="80"></textarea><br><br>
        <label>Link do tabulatury </label><br>
          <input type="text" class="ae-input" name="ae_tabLink" placeholer="url do tabów"><br>
        <label>Data rozpoczęcia wydarzenia* </label><br>
          <input type="date" class="ae-input" name="ae_startEvent"><br>
        <label>Data zakończenia wydarzenia* </label><br>
          <input type="date" class="ae-input" name="ae_endEvent"><br>
        <label>Tagi </label><br>

          <input type="text" class="ae-input" name="tags" placeholder="Max. 6, słowa oddzielaj ' - ', a tagi ' , '"><br>
                <label>Pola oznaczone gwiazdką (*) są obowiązkowe.<label><br>
        <input type="submit" class="ae-submit" name="ae_addEvent" value="Utwórz">
      </form>
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
      if(isset($_SESSION["add_event_error"]) && $_SESSION["add_event_error"]!="") {
         ?><script>alert("<?php echo $_SESSION["add_event_error"];?>")</script><?php
         unset($_SESSION["add_event_error"]);
       }
    ?>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
