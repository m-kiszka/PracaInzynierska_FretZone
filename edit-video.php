<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <title>Edytuj wykonanie - FretZone</title>
  </head>
  <body>
    <?php
    include('./scripts/scriptCookiesBanner.html');
    if(isset($_SESSION["perf_id"]))
    {
      $video = array();
      $video[0]=$_SESSION["perf_id"];
      $video[1]=$_SESSION["perf_name"];
      $video[2]=$_SESSION["perf_desc"];
      unset($_SESSION["perf_name"]);
      unset($_SESSION["perf_desc"]);
      unset($_SESSION["perf_type"]);
      unset($_SESSION["perf_url"]);
      $_SESSION["perf_change"]=1;
    }
    else
    {
      unset($_SESSION["perf_id"]);
      unset($_SESSION["perf_name"]);
      unset($_SESSION["perf_desc"]);
      unset($_SESSION["perf_type"]);
      unset($_SESSION["perf_url"]);
      unset($_SESSION["perf_change"]);
      ?> <script type="text/javascript">location.href = './videos-list.php';</script> <?php
      die();
    }
    ?>
    <!-- banner -->
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Edytuj wykonanie</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

      <div class="ev-container">
        <form action="./scripts/scriptEditVideo.php" class="ev-form" method="post" enctype="multipart/form-data">
          <label>Nazwa:</label><br>
          <input type="text" class="ev-input ev-text" name="name" value="<?php echo $video[1]?>"><br><br>
          <label>Opis:</label><br>
          <textarea class="ev-input" name="description" rows="8" cols="50"><?php echo $video[2]; ?></textarea><br>
          <input type="submit" class="ev-submit ev-edit" name="submitVideo" value="Edytuj"/> <!-- zielony -->
          <a href="./play.php?v=<?php echo $video[0]; ?>"><button type="button" class="ev-submit">Wróć</button></a>
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
    if(isset($_SESSION["register_error"]) && $_SESSION["register_error"]!="") {
       ?><script>alert("<?php echo $_SESSION["register_error"];?>")</script><?php
       unset($_SESSION["register_error"]);
     } ?>
     <footer>
      <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
     </footer>
  </body>
</html>
