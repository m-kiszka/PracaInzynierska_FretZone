<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/style2.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <title>Edytuj tabulaturę - FretZone</title>
  </head>
  <body>
    <?php
      include('./scripts/scriptCookiesBanner.html');
    if(isset($_SESSION["tab_id"]))
    {
      $tab = array();
      $tab[0]=$_SESSION["tab_id"];
      $tab[1]=$_SESSION["tab_name"];
      $tab[2]=$_SESSION["tab_desc"];
      $tab[3]=$_SESSION["tab_tab"];
      $_SESSION["tab_change"]=1;
    }
    else
    {
      unset($_SESSION["tab_id"]);
      unset($_SESSION["tab_name"]);
      unset($_SESSION["tab_desc"]);
      unset($_SESSION["tab_tab"]);
      unser($_SESSION["tab_change"]);
      ?> <script type="text/javascript">location.href = './tabs-list.php';</script> <?php
      die();
    }

    ?>
    <!-- banner -->
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Edytuj tabulaturę</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <script>
      $(document).ready(function()
      {
        $("#tabsTextArea").focusout(function()
        {
          var reqValue = $("#tabsTextArea").val();
          if(reqValue.length)
          {
            $.get("./scripts/scriptTabPreview.php", {requested: reqValue}).done(function(data)
            {
              console.log(data);
              $("#tabsTextArea-Preview").html(data);
            });
          }
        });
      });
    </script>

    <!-- container -->
    <div class="ae-container">
      <!-- <div class="error">
        <?php
             /* if(isset($_SESSION["add_tabs_error"]))
            {
              echo $_SESSION["add_tabs_error"];
              unset($_SESSION["add_tabs_error"]);
            } */
          ?>
      </div> -->
      <form class="at-form tabsForm" action="./scripts/scriptEditTabs.php" method="post">

        <label>Tytuł*</label><br>
        <input type="text" class="at-input" name="tabsTitle" value="<?php echo $tab[1]; ?>"><br>

        <label>Opis*</label><br>
        <input type="text" class="at-input" name="tabsDesc" value="<?php echo $tab[2]; ?>"><br>

        <label>Tabulatura (według standardu <a href="https://en.wikipedia.org/wiki/ASCII_tab">tabulatur ASCII</a>, obsługuje formatowanie <a href="./formatting-guide.php" target="_blank">BBCode</a>)*</label><br>

        <textarea name="tabsTextArea" class="at-textarea" id="tabsTextArea">
          <?php echo $tab[3]; ?>
        </textarea><br><br>

          <br><label>Podgląd:</label><br><br>
        <!-- ajax -->
        <div id="tabs-preview">
          <span name="tabsTextArea-Preview" id="tabsTextArea-Preview"></span><br>
        </div>

        <input type="checkbox" name="tabsOwner"> Potwierdzam, że jestem autorem tabulatury.*<br>

        <label>Pola oznaczone gwiazdką (*) są obowiązkowe.<label><br>
        <input type="submit" class="ee-submit" name="ae_addtab" value="Edytuj" />
      </form>
      <a href="./tab.php?v=<?php echo $tab[0]; ?>"><button type="button" class="ee-submit">Wróć</button></a>
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
    <?php
    if(isset($_SESSION["add_tabs_error"]) && $_SESSION["add_tabs_error"]!="") {
       ?><script>alert("<?php echo $_SESSION["add_tabs_error"];?>")</script><?php
       unset($_SESSION["add_tabs_error"]);
     }
     ?>
     <footer>
      <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
     </footer>
  </body>
</html>
