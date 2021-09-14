<?php
session_start();
session_regenerate_id(true);
require_once('./scripts/scriptConnect.php');
require_once('./scripts/scriptCheck_LoggedIn.php');
?>
<!DOCTYPE html>
<?php

$userid = $_SESSION["userid"];
$login = $_SESSION["login"];

$result = $connect->query("SELECT name_surname, birth_date, localization FROM fz_users WHERE id='$userid'");
$result = $result->fetch_row();
?>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <title>Personalizacja - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Personalizacja</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <div class="ep-container">
      <!-- <div class="error">
        <?php
        /*  if(isset($_SESSION["upload-error"]))
          {
            echo $_SESSION["upload-error"];
            unset($_SESSION["upload-error"]);
          } */
        ?>
      </div> -->
      <div class="editProfile">

        <button onclick="window.location.href='./edit-profile.php'" class="ep-submit ep-p" style="background: purple;">Personalizacja</button>
        <button onclick="window.location.href='./edit-account.php'" class="ep-submit ep-p" style="background: white; color: purple; border: 1px solid purple;">Dane logowania</button>


    <!-- przesylanie avatara -->
        <form action="./scripts/scriptUploadAvatar.php" class="ep-form" method="post" enctype="multipart/form-data">
          Wybierz plik formatu PNG lub JPG, o <u>rekomendowanych wymiarach 300x300</u><br><br> i <b>maksymalnym rozmiarze</b> pliku 500 KB:<br><br>
          <input type="file" class="input-browse-hidden" name="fileToUpload" id="fileToUpload">
          <div class="input-browse" id="uploadTrigger">Przeglądaj...</div>
          <input type="submit" class="ep-submit ep-upload" value="Wyślij avatar" name="submitPhoto">
        </form><br>
        <form action="./scripts/scriptEditProfile.php" class="ep-form" method="post">
          <label>Imię i nazwisko</label><br>
          <input type="text" class="ep-input" name="name" value="<?php echo $result[0]; ?>"><br>
          <label>Data urodzenia</label><br>
          <input type="date" class="ep-input" name="birthday" value="<?php echo $result[1]; ?>"><br>
          <label>Lokalizacja</label><br>
          <input type="text" class="ep-input" name="city" value="<?php echo $result[2]; ?>"><br>
          <input type="submit" class="ep-submit" value="Zapisz">
        </form>
      </div>
      <!-- menu mobilne -->
        <nav class="nav-bottom">
          <a href="#" class="nav-item">
          <i class='icon icon-logout fas fa-power-off'></i>
            <span class="menu-text">Wyloguj</span>
          </a>
          <a href="#" class="nav-item">
          <i class='icon fas fa-cog'></i>
            <span class="menu-text">Ustawienia</span>
          </a>
          <a href="#" class="nav-item">
          <i class='icon fas fa-user-alt'></i>
            <span class="menu-text">Mój profil</span>
          </a>
        <!-- <a href="./moderator.php"><i class='icon fas fa-tachometer-alt'></i></a> -->
      </nav>
    </div>
    <script>
    $("#uploadTrigger").click(function(){
     $("#fileToUpload").click();
    });
    </script>
    <!-- Komunikat o błędzie -->
    <?php
      if(isset($_SESSION["upload-error"]) && $_SESSION["upload-error"]!="") {
         ?><script>alert("<?php echo $_SESSION["upload-error"];?>")</script><?php
         unset($_SESSION["upload-error"]);
       }
    ?>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
