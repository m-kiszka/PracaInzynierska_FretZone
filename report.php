<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<script src="./resources/jquery-3.5.1.min.js"></script>

<?php
$report_id=$_GET["id"];

$result = $connect->query("SELECT id, report_type, description, added_date, user_id, url, status FROM fz_reports WHERE id='$report_id'");
$result = $result->fetch_row();

if($result==null)
{
  $_SESSION["report_error"] = "Zgłoszenie nie istnieje.";
  ?> <script type="text/javascript">location.href = './reports-list.php';</script> <?php
  die();
}

if(isset($_SESSION["userid"]))
{
  $id = $_SESSION["userid"];
  $rank_result = $connect->query("SELECT login, user_rank FROM fz_users WHERE id='$id'");
  unset($id);
  $rank_result = $rank_result->fetch_row();
  if(!isset($rank_result) || $rank_result[1]=="" || $rank_result[1]<3)
  {
    ?> <script type="text/javascript">location.href = './index.php';</script> <?php
    die();
  }
  else
  {
    if($result[6]=="Open")
    {
      $connect->query("UPDATE fz_reports SET status='$rank_result[0]' WHERE id='$report_id'");
      $result[6]=$rank_result[0];
    }
  }
}
else
{
  ?> <script type="text/javascript">location.href = './index.php';</script> <?php
  die();
}
?>

<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <title>Zgłoszenie - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <!-- <link rel="stylesheet" href="./css/style2.css"> -->
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>
    <!-- banner -->
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Zgłoszenie</p>

      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>
    <div class="ul-container">
      <h2><?php echo "Zgłoszenie: ".$result[1]; ?></h2>
      <div class="r-container">
        <div class="r-left">
        <?php
        //STATUS ZGŁOSZENIA
        if($result[6]!="Open" && $result[6]!="Closed")
        { ?>
          <b>Przyjęte przez:</b> <a href="./profile.php?p=<?php echo $result[6]; ?>"><?php echo $result[6]; ?></a>
  <?php }
        if($result[6]=="Open")
        {
          ?>Zgłoszenie otwarte.<?php
        }
        if($result[6]=="Closed")
        {
          ?>Zgłoszenie zamknięte.<?php
        }
        //STATUS ZGŁOSZENIA

        ?><br><?php echo "<b>Data utworzenia: </b>".$result[3];?><br><?php

        echo "<b>Typ: </b>".$result[1];?><br>
        <?php if($result[6]!="Open" && $result[6]!="Closed" && ($result[6]==$rank_result[0] || $rank_result[1]>3))
        { ?>
        <a href="./scripts/scriptEditReport.php?id=<?php echo $result[0]; ?>"><button class="at-button r-button">Zamknij zgłoszenie</button></a>
  <?php } ?>
        </div>
        <div class="r-right"><?php

        $temp = $result[4];
        $login = $connect->query("SELECT login FROM fz_users WHERE id='$temp'");
        $login = $login->fetch_row();

        if($login==null || $login[0]=="")
        {
          $login = "Brak informacji";
        }
        ?>

        <b>Zgłaszający:</b> <a href="./profile.php?p=<?php echo $login[0] ?>"><?php echo $login[0] ?></a><br>

        <b>Zgłoszona strona:</b> <a href="<?php echo $result[5]; ?>"><?php echo $result[5]; ?></a><br><br>

        <b>Opis: </b><?php echo $result[2];?><br>
        </div>
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
  </div>
  <footer>
   <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
  </footer>
  </body>
</html>
