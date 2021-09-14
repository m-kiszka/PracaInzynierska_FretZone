<?php session_start(); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_Moderator.php'); ?>
<!DOCTYPE html>

<html lang="en" dir="ltr">
  <head>
    <link rel="stylesheet" href="./css/style2.css">
        <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <meta charset="utf-8">
    <title>Panel moderacji - FretZone</title>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Panel moderacji</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <!-- kontener -->
    <div class="m-main-container">
      <h2>Panel moderacji</h2>
      <div class="m-container">
        <a href="./users-list.php">
          <div class="manage-item">
            <i class='fas fa-user-alt'></i>
            <h4 class="m-h4">Użytkownicy</h4>
          </div>
        </a>
        <a href="./reports-list.php">
          <div class="manage-item">
            <i class='fas fa-exclamation-triangle'></i>
            <h4 class="m-h4">Zgłoszenia</h4>
          </div>
        </a>
        <a href="./videos-list.php">
            <div class="manage-item">
            <i class='fas fa-video'></i>
            <h4 class="m-h4">Filmy</h4>
          </div>
        </a>
      </div>

      <div class="m-container">
        <a href="./events-list.php">
            <div class="manage-item">
            <i class='fas fa-calendar-alt'></i>
            <h4 class="m-h4">Wydarzenia</h4>
          </div>
        </a>
        <a href="./tags-list.php">
          <div class="manage-item">
            <i class='fas fa-sticky-note'></i>
            <h4 class="m-h4">Tagi</h4>
          </div>
        </a>
        <a href="./tabs-list.php">
          <div class="manage-item">
            <i class='fas fa-edit'></i>
            <h4 class="m-h4">Tabulatury</h4>
          </div>
        </a>
      </div>
    </div>

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
  <footer>
     <p>Copyright &copy 2020-2021 FretZone i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
