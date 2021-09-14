<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/style2.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <title>Edytuj wydarzenie - FretZone</title>
  </head>
  <body>
    <?php
    include('./scripts/scriptCookiesBanner.html');

    if(isset($_SESSION["event_id"]))
    {
      $event = array();
      $event[0]=$_SESSION["event_id"];
      $event[1]=$_SESSION["event_name"];
      $event[2]=$_SESSION["event_desc"];
      $event[3]=$_SESSION["event_tab"];
      $_SESSION["event_change"]=1;
    }
    else
    {
      unset($_SESSION["event_id"]);
      unset($_SESSION["event_name"]);
      unset($_SESSION["event_desc"]);
      unset($_SESSION["event_tab"]);
      unset($_SESSION["event_change"]);
      ?> <script type="text/javascript">location.href = './events-list.php';</script> <?php
      die();
    }

    ?>
    <!-- banner -->
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Edytuj wydarzenie</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <!-- container -->
    <div class="el-container">
      </div>
      <form class="ee-form" action="./scripts/scriptEditEvent.php" method="post">
        <span class="ee-span">Nazwa wydarzenia: </span><br>
          <input type="text" class="ee-input" name="ae_title" value="<?php echo $event[1]; ?>"><br><br>
        <span class="ee-span">Opis (obsługuje formatowanie BBCode, po więcej zobacz nasz <a href="./formatting-guide.php" target="_blank">przewodnik</a>): </span><br>
          <textarea class="textarea" name="ae_description" rows="8" cols="80"><?php echo $event[2]; ?></textarea><br><br>
        <span class="ee-span">Link do tabulatury (opcjonalnie): </span class="ee-span"><br>
          <input type="text" class="ee-input" name="ae_tabLink" value="<?php echo $event[3]; ?>"><br><br>
        <input type="submit" class="ee-submit ee-edit" name="ae_addEvent" value="Edytuj" />
      </form>
      <a href="./event.php?v=<?php echo $event[0]; ?>"><button type="button" class="ee-submit">Wróć</button></a>
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

    <!-- Komunikat o błędzie -->
    <?php
      if(isset($_SESSION["add_event_error"]) && $_SESSION["add_event_errorr"]!="") {
         ?><script>alert("<?php echo $_SESSION["add_event_error"];?>"); </script><?php
         unset($_SESSION["add_event_error"]);
       }
    ?>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
