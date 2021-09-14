<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Rejestracja - FretZone</title>
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/style2.css">
  </head>

  <?php if(isset($_SESSION["userid"]))
  {
    ?> <script type="text/javascript">location.href = './index.php';</script> <?php
    die();
  } ?>

  <!-- skrypt jquery od sprawdzania unikalnosci loginu -->
    <script>
      jQuery.noConflict();
      jQuery(document).ready(function()
      {
        //spradzanie czy login juz istnieje w bazie
        jQuery("input[name='login']").focusout(function()
        {
          var reqValue = jQuery(this).val();
          if(reqValue.length)
          {
            jQuery.get("./scripts/scriptLoginCheck.php", {requested: reqValue}).done(function(data)
            {
              //console.log(data);
              jQuery(".error_text_login").text(data);
            });
          }
        });
        //sprawdzanie czy email juz istnieje w bazie
        jQuery("input[name='email']").focusout(function()
        {
          var reqValue = jQuery(this).val();
          if(reqValue.length)
          {
            jQuery.get("./scripts/scriptEmailCheck.php", {requested: reqValue}).done(function(data)
            {
              //console.log(data);
              jQuery(".error_text_email").text(data);
            });
          }
        });
      });
    </script>

  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Rejestracja</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <!-- <div class="error">
      <?php /* if(isset($_SESSION["register_error"]) && $_SESSION["register_error"]!="") {  echo $_SESSION["register_error"]; unset($_SESSION["register_error"]); } */ ?>
    </div> -->
    <div class="rg-container">
      <h3 class="header3">Aby korzystać z serwisu FretZone, dokończ rejestrację.</h3>
        <form class="register_form" action="./scripts/scriptExternalValidator.php" method="post">
          <br>
          <div class=error_text_login></div>
          <input class="register_input" type="login" name="login" placeholder="Wpisz login...">

          <div class=error_text_email></div>
          <input class="register_input" type="email" name="email" placeholder="Wpisz adres e-mail...">
          <input class="register_input" type="email" name="confirm_email" placeholder="Powtórz adres e-mail...">
          <p><input class="register_checkbox" type="checkbox" name="terms"> Akceptuję <a href="./terms.php" style="color: #000;">regulamin</a> serwisu</p>
          <input class="register_submit" type="submit" name="register_submit" value="Zarejestruj">

        </form>
    </div>
    <?php
      if(isset($_SESSION["register_error"]) && $_SESSION["register_error"]!="") {
    ?>  <script> alert("<?php echo $_SESSION["register_error"]; ?>"); </script> <?php unset($_SESSION["register_error"]);
      }
     ?>
     <footer>
      <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
     </footer>
  </body>
</html>
