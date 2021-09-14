<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Dodaj tabulaturę - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
        <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./font-awesome/css/all.css">
  </head>
  <body>
  <?php include('./scripts/scriptCookiesBanner.html');?>
    <!-- modal -->
    <!-- The Modal -->
      <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
          <span class="close">&times;</span>
          <?php if(isset($_SESSION["userid"])) { include("./formatting-guide.php"); } else { echo "Musisz się zalogować, aby zobaczyć formatowanie."; } ?>
        </div>
      </div>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Dodaj tabulaturę</p>
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

    <div class="at-container">
      <div class="error">
        <?php
            if(isset($_SESSION["add_tabs_error"]))
            {
              echo $_SESSION["add_tabs_error"];
              unset($_SESSION["add_tabs_error"]);
            }

            if(isset($_SESSION["add_tab_temp"]))
            {
              $tab_temp = $_SESSION["add_tab_temp"];
              unset($_SESSION["add_tab_temp"]);
            }
         ?>
      </div>
      <form class="at-form tabsForm" action="./scripts/scriptAddTabs.php" method="post">

        <label>Tytuł*</label><br>
        <input type="text" class="at-input" name="tabsTitle" placeholder="Podaj tytuł..."><br>

        <label>Opis*</label><br>
        <input type="text" class="at-input" name="tabsDesc" placeholder="Wpisz opis..."><br>

        <label>Tabulatura (według standardu <a href="https://en.wikipedia.org/wiki/ASCII_tab">tabulatur ASCII</a>, obsługuje formatowanie <b id="myBtn" style="text-decoration: underline;">BBCode</b>)*</label><br>
<textarea name="tabsTextArea" class="at-textarea" id="tabsTextArea">
<?php if(isset($tab_temp) && $tab_temp!="")
{
  echo $tab_temp;
}
else
{ ?>
  [b]Przykładowy szablon[/b]

  Strój: EBGDAE/EADGBE
  Tempo: 70 bpm
  Kapodaster: 3 próg

  e |---------------------------------------|
  B |---------------------------------------|
  G |---------------------------------------|
  D |---------------------------------------|
  A |---------------------------------------|
  E |---------------------------------------|
<?php } ?>
</textarea><br><br>
          <br><label>Podgląd:</label><br><br>
        <!-- ajax -->
        <div id="tabs-preview">
          <span name="tabsTextArea-Preview" id="tabsTextArea-Preview"></span><br>
        </div><br><br>

        <label>Tagi</label><br>
          <input type="text" class="at-input" name="tags" placeholder="Max. 6, słowa oddzielaj ' - ', a tagi ' , '"><br>

        <input type="checkbox" name="tabsOwner"> Potwierdzam, że jestem autorem tabulatury.*<br><br>

        <label style="font-size: 12px;">Pola oznaczone gwiazdką (*) są obowiązkowe.<label><br>
        <input type="submit" class="at-button add-tab" name="tabsSubmit" value="Dodaj tabulaturę">
      </form>

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
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>

    <script type="text/javascript">

      var modal = document.getElementById("myModal");
      var btn = document.getElementById("myBtn");
      var span = document.getElementsByClassName("close")[0];
      btn.onclick = function()
      {
        modal.style.display = "block";
      }

      span.onclick = function()
      {
        modal.style.display = "none";
      }

      window.onclick = function(event)
      {
        if (event.target == modal)
        {
          modal.style.display = "none";
        }
      }
    </script>

  </body>
</html>
