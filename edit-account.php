<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <title>Dane logowania - FretZone</title>
  </head>
  <body>
  <?php include('./scripts/scriptCookiesBanner.html');

      $temp = $connect->query("SELECT password FROM fz_users WHERE id='$temp_session'");
      $temp = $temp->fetch_row();
      if(isset($temp[0]))
      {
        if($temp[0]!="")
        {
          $temp="1";
        }
        else {
          $temp="0";
        }
      }
      else
      {
        $temp="0";
      }
      ?><script type="text/javascript">
        $(document).ready(function()
        {
          let temp = "<?php echo $temp; ?>";
          if(temp==0)
          {
            $("#pass_form").hide();
            $("#no_pass_form").hide();
            $("#create_pass").show();
          }
          else
          {
            $("#pass_form").show();
            $("#no_pass_form").hide();
            $("#create_pass").hide();
          }

          $("#create_pass").click(function()
          {
            $("#no_pass_form").show();
            $("#pass_form").hide();
            $("#create_pass").hide();
          });
        });
        </script> <?php
    ?>
    <!-- banner -->
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Dane logowania</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <div class="ep-container" style="text-align: center;">
    <div class="editProfile" style="margin-top: -50px;">
      <button onclick="window.location.href='./edit-profile.php'" class="ep-submit ep-p" style="background: white; color: purple; border: 1px solid purple;">Personalizacja</button>
      <button onclick="window.location.href='./edit-account.php'" class="ep-submit ep-p" style="background: purple;">Dane logowania</button>
    </div>
      <!-- zmiana maila -->
      <form class="form ea-form" action="./scripts/scriptChangeEmail.php" method="post">
        <label>Obecny e-mail:</label>
        <?php
          $userid = $_SESSION["userid"];
          $result = $connect->query("SELECT email FROM fz_users WHERE id='$userid'");
          $result = $result->fetch_row();

          //zamiana poszczególnych znaków w wyświetlanym emailu, aby utrudnić jego odczytanie niepożądanym osobom
          for($i=0;$i<strlen($result[0]);$i++)
          {
            if($i!=0 && $i!=strlen($result[0])-1 && $i!=strpos($result[0],"@") && $i!=strpos($result[0],"@")-1 && $i!=strpos($result[0],"@")+1)
            {
              $result[0][$i]="*";
            }
          }
          ?><br>
        <span id="info"><?php echo $result[0]; ?></span><br><br>
        <br>
        <label>Podaj hasło:</label><br>
        <input type="password" class="ep-input" name="ep_oldPassword"><br>
        <label>Nowy e-mail:</label><br>
        <input type="text" class="ep-input" name="ep_mail" value=""><br>
        <input type=submit class="ep-submit" value="Zmień e-mail">
      </form>
      <!-- zmiana hasła -->
      <form class="form ea-form" action="./scripts/scriptChangePassword.php" method="post" id="pass_form">
        <label>Stare hasło:</label><br>
        <input type="password" class="ep-input" name="ep_oldPassword"><br>
        <label>Nowe hasło:</label><br>
        <input type="password" class="ep-input" name="ep_password"><br>
        <label>Powtórz nowe hasło:</label><br>
        <input type="password" class="ep-input" name="ep_repeatPassword"><br>
        <input type=submit class="ep-submit" value="Zmień hasło"><br>
      </form>
      <a href="./remove-account.php"><button class="ep-submit deleteAccount">Usuń konto</button></a><br>
      <form class="form ea-f" action="./scripts/scriptCreatePassword.php" method="post" id="no_pass_form">
        <label>Hasło:</label><br>
        <input type="password" class="ep-input" name="ep_password"><br>
        <label>Powtórz hasło:</label><br>
        <input type="password" class="ep-input" name="ep_repeatPassword"><br>
        <input type=submit class="ep-submit" value="Ustaw hasło">
      <button class="ep-submit" id="create_pass">Utwórz hasło</button><br>
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

    <?php
      if(isset($_SESSION["reset_error"]) && $_SESSION["reset_error"]!="") {
        ?> <script> alert("<?php echo $_SESSION["reset_error"]; ?>"); </script> <?php
         unset($_SESSION["reset_error"]);
       } ?>
       <footer>
		    <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
	     </footer>
  </body>
</html>
