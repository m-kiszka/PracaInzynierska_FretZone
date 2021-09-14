<?php
session_start();
session_regenerate_id(true);
require_once('./scripts/scriptConnect.php');
require_once('./scripts/scriptCheck_Moderator.php');
?>
<!DOCTYPE html>
<?php
if(!isset($_GET["p"]))
{
  ?> <script type="text/javascript">location.href = './index.php';</script> <?php
  die();
}

$temp = $_GET["p"];
$result = $connect->query("SELECT id, login, avatar_url, user_rank FROM fz_users WHERE login='$temp'");
$result = $result->fetch_row();

if($result==null || $result[0]=="")
{
  ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
  die();
}

$_SESSION["edit_user_login"]=$result[1];
?>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <title>Edytuj użytkownika - FretZone</title>
  </head>

  <!-- ustawienie selectów do odpowiednich wartości -->
  <script type="text/javascript">
    $(window).on('load', function()
    {
      document.getElementById("user_rank").value = "<?php echo $result[3]; ?>";
    });
  </script>

  <body>
  <?php include('./scripts/scriptCookiesBanner.html'); ?>
  <div class="banner">
    <a title="Strona główna" href="./index.php">
      <div class="logo"></div>
    </a>
      <p class="logo-text">Edytuj użytkownika</p>
    <?php include('./scripts/scriptBannerButtons.php'); ?>
  </div>

    <div class="ep-container">
      <div class="editProfile">

      <?php //avatar
      if($result[2]!="")
      { ?>
        <div><img class="p_profile_img" src="<?php echo "./uploads/avatars/".$result[2]; ?>" alt="profile_img"></div>
<?php }
      else
      { ?>
          <div><img class="p_profile_img" src="./uploads/avatars/default.png" alt="profile_img"></div>
<?php } ?>
<!--$rank_result = ranga edytującego | $result[3] = ranga moderowanego -->
      <form class="change_rank" action="./scripts/scriptEditUser.php" method="post">
        <input type="text" name="user_login" disabled value="<?php echo $result[1]; ?>">
        <select name="user_rank" class="rank-select" id="user_rank">
          <option value="-1" <?php if($rank_result[0]<3) { echo "disabled"; } ?>>Zawieszony</option>
          <option value="0" disabled>Niezweryfikowany</option>
          <option value="1" <?php if($rank_result[0]<3) { echo "disabled"; } ?>>Użytkownik</option>
          <option value="3" <?php if($rank_result[0]<4) { echo "disabled"; } ?>>Moderator</option>
          <option value="4" <?php if($rank_result[0]<4) { echo "disabled"; } ?>>Administrator</option>
        </select>
        <input type="submit" value="Zmień">
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

    <?php
      if(isset($_SESSION["edit_user_error"]))
      {
        ?> <script> alert(" <?php echo $_SESSION["edit_user_error"]; ?>"); </script> <?php
        unset($_SESSION["edit_user_error"]);
      }
    ?>
    <footer>
     <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
    </footer>
  </body>
</html>
