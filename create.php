<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>

<html lang="en" dir="ltr">
  <head>
    <link rel="stylesheet" href="./css/style2.css">
        <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <meta charset="utf-8">
    <title>Panel tworzenia - FretZone</title>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html');?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Panel tworzenia</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <!-- kontener -->
    <div class="m-main-container">
      <h2>Panel użytkownika</h2>
      <div class="m-container">
        <a href="./add-event.php">
          <div class="manage-item">
            <i class='fas fa-trophy'></i>
            <h4 class="m-h4">Stwórz wydarzenie</h4>
          </div>
        </a>
        <a href="./add-tabs.php">
          <div class="manage-item">
            <i class='fas fa-pen-alt'></i>
            <h4 class="m-h4">Utwórz tablulaturę</h4>
          </div>
        </a>
        <a href="./upload-video.php">
            <div class="manage-item">
            <i class='fas fa-file-video'></i>
            <h4 class="m-h4">Dodaj wykonanie</h4>
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
   <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
  </footer>
  </body>
</html>
