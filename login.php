<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr" style="overflow: hidden;">
  <head>
    <meta charset="utf-8">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <meta name="google-signin-client_id" content="438338313435-im2gn4blhcs67olph99b74niaoj4mu0u.apps.googleusercontent.com">
    <title>Logowanie - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
  </head>
  <body style="background: #fff; color: #000;">

    <?php include('./scripts/scriptCookiesBanner.html');

    if(isset($_SESSION["userid"]))
    {
      ?> <script type="text/javascript">location.href = './index.php';</script> <?php
      die();
    } ?>

    <script type="text/javascript">
      $(document).ready(function()
      {
        $(".g-signin2").show();
        $(".google-login").hide();
        $(".google-register").hide();
      });

      function onSignIn(googleUser)
      {
        var profile = googleUser.getBasicProfile();
        var id_token = googleUser.getAuthResponse().id_token;

        //token
        var xhr = new XMLHttpRequest();
        xhr.open('POST', './scripts/scriptTokenSignIn.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('idtoken=' + id_token);
        xhr.onload = function()
        {
          var response = xhr.responseText;
          if(response==0) //użytkownik nie istnieje w bazie
          {
            $(".g-signin2").hide();
            $(".google-login").hide();
            $(".google-register").show();
          }
          else if(response==1)
          {
            $(".g-signin2").hide();
            $(".google-login").show();
            $(".google-register").hide();
          }
          else if(response!=1 && response!=0)
          {
            $(".g-signin2").show();
            $(".google-login").hide();
            $(".google-register").hide();
          }
        };

      }
    </script>

    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Logowanie</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <div class="container" style="overflow: scroll;">

      <div class="login-banner">
        <h3 style="text-align: center;">Posiadanie konta w FretZone pozwala korzystać z innych funkcji:</h3>

          <h3><br>
          <ul style="padding-left: 10px;">
            <li>Oceniać wykonania użytkowników,</li>
            <li>Brać udział w wydarzeniach,</li>
            <li>Tworzyć własne wydarzenia,</li>
            <li>Dodawać tabulatury,</li>
            <li>I inne...</li>
          </ul>
         </h3>
         <br>
         <h3 style="text-align: center;">Zaloguj się i korzystaj z dodatkowych możliwości!</h3>
      </div>
      <div class="login_form">
        <form class="form_not_log_in" action="./scripts/scriptLoginValidator.php" method="post">
          <h2 style="color: #000; margin-bottom: 100px; font-size: 48px; font-family:'Montserrat';">Dołącz do naszej gitarowej społeczności.</h2>
          <input type="login" class="login-input input-first" name="login" placeholder="Nazwa użytkownika">
          <input type="password" class="login-input" name="password" placeholder="Hasło">
          <p><a class="forgot_password" href="forgot-password.php">Nie pamiętam hasła</a></p>
          <input class="login-button" type="submit" name="submit" value="Zaloguj">
          <p>lub</p><br>
          <center><div class="g-signin2" style="border-radius: 10px; !important" data-width="200" data-height="50" data-onsuccess="onSignIn"></div></center>
          <!--Zarejestruj się kontem Google-->
          <div class="google-register"><a href="./register-google.php"><img src="./images/icons/btn_google_signin_light_normal_web@2x.png"></img></a></div>
          <!--Zaloguj się kontem Google-->
          <div style="border-radius: 10px !important;" class="google-login"><a href="./scripts/scriptLoginExternal.php"><img style="border-radius: 10px !important;" src="./images/icons/btn_google_signin_light_normal_web@2x.png"></a></div>
          <p>Nie masz jeszcze konta? <a href="./register.php" style="color: black;">Zarejestruj się tutaj!</a></p>

        </form>
      </div>
        <!-- STOPKA -->
        <!-- <footer>
          <div class="footerItem">
            <p>kolumna 1</p>
          </div>
          <div class="footerItem">
            <p>kolumna 2</p>
          </div>
          <div class="footerItem">
            <p>kolumna 3</p>
          </div>
        </footer> -->
      </div>

      <!-- Komunikat o błędzie -->
      <?php
      if(isset($_SESSION["login_error"]) && $_SESSION["login_error"]!="")
      {
        ?><script> alert("<?php echo $_SESSION["login_error"];?> "); </script><?php
        unset($_SESSION["login_error"]);
      }
      elseif(isset($_SESSION["register_error"]) && $_SESSION["register_error"]!="")
      {
        ?><script> alert("<?php echo $_SESSION["register_error"];?>"); </script><?php
        unset($_SESSION["register_error"]);
      }
      ?>
      <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
