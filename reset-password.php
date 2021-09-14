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
    if(isset($_SESSION["verify_code"]))
    {
      unset($_SESSION["verify_code"]);
    }

    if(isset($_SESSION["userid"]))
    {
      ?> <script type="text/javascript">location.href = './index.php';</script> <?php
      die();
    }
    if(!isset($_GET["v"]))
    {
      ?> <script type="text/javascript">location.href = './index.php';</script> <?php
      die();
    }
    else
    {
      $verify_code=$_GET["v"];
      $verify_code=htmlspecialchars($verify_code);
      $result = $connect->query("SELECT verify_code, login FROM fz_users WHERE verify_code='$verify_code'");
      $result = $result->fetch_row();
      if(!isset($result) || $result[0]=="")
      {
        ?> <script type="text/javascript">location.href = './index.php';</script> <?php
        die();
      }
    }

    $_SESSION["verify_code"]=$verify_code;
    ?>

    <!-- <div class="error">
      <?php /* if(isset($_SESSION["reset_error"]) && $_SESSION["reset_error"]!="") {  echo $_SESSION["reset_error"]; unset($_SESSION["reset_error"]); } */ ?>
    </div> -->

    <div class="ep-container" style="text-align: center;">
      <form class="forgot_form" action="./scripts/scriptResetPassword.php" method="post">
        <label>Podaj nowe hasło:</label>
        <br>
        <input type="password" class="password_input" name="password_reset"><br>
        <label>Powtórz hasło:</label><br>
        <input type="password" class="password_input" name="confirm_reset"><br>
        <input type="submit" value="Zresetuj hasło"><br>
      </form>
      <!-- Komunikat o błędzie -->
      <?php
      if(isset($_SESSION["reset_error"]) && $_SESSION["reset_error"]!="") {
         ?><script>alert("<?php echo $_SESSION["reset_error"];?>")</script><?php
         unset($_SESSION["reset_error"]);
       } ?>
   </div>
  </body>
</html>
