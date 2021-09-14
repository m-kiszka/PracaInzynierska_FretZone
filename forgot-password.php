<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <title>Przypomnij hasło - FretZone</title>
  </head>
  <body>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Przypomnij hasło</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <?php
    include('./scripts/scriptCookiesBanner.html');
    if(isset($_SESSION["userid"]))
    {
      ?> <script type="text/javascript">location.href = './index.php';</script> <?php
      die();
    }
    ?>
      <div class="ep-container" style="text-align: center;">
      <form class="forgot_form" action="./scripts/scriptForgotPassword.php" method="post">
        <label>Podaj adres e-mail, na który zostało zarejestrowane konto:</label>
        <br>
        <input type="email" class="forgot_input" name="forgot_password"><br>
        <input type="submit" class="forgot_submit" value="Przypomnij hasło"><br>
      </form>
      <?php
        if(isset($_SESSION["forgot_error"]) && $_SESSION["forgot_error"]!="")
        {
          ?> <script> alert("<?php echo $_SESSION["forgot_error"]; ?>");</script><?php
          unset($_SESSION["forgot_error"]);
        }
      ?>
        </div>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
