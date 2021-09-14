<?php
  session_start();
  require_once('./scriptConnect.php');
  require_once('./scriptCheck_LoggedIn_Script.php');

  $name_surname = $_POST["name"];
  $birthday = $_POST["birthday"];
  $city = $_POST["city"];
  $user_id=$_SESSION["userid"];

  $name_surname=htmlspecialchars($name_surname);
  $birthday=htmlspecialchars($birthday);
  $city=htmlspecialchars($city);
  $user_id=htmlspecialchars($user_id);

  if(strlen($name_surname)>128)
  {
    $_SESSION["upload-error"] = "Wprowadzone dane nie mogą zajmować więcej niż 128 znaków.";
    ?> <script type="text/javascript">location.href = '../edit-profile.php';</script> <?php
    die();
  }

  if(strlen($birthday)>128)
  {
    $_SESSION["upload-error"] = "Wprowadzone dane nie mogą zajmować więcej niż 128 znaków.";
    ?> <script type="text/javascript">location.href = '../edit-profile.php';</script> <?php
    die();
  }

  if($birthday>date("Y-m-d"))
  {
    $_SESSION["upload_error"] = "Wprowadzona data urodzenia nie jest poprawna.";
    ?> <script type="text/javascript">location.href = '../edit-profile.php';</script> <?php
    die();
  }

  if(strlen($city)>128)
  {
    $_SESSION["upload-error"] = "Wprowadzone dane nie mogą zajmować więcej niż 128 znaków.";
    ?> <script type="text/javascript">location.href = '../edit-profile.php';</script> <?php
    die();
  }

  if($connect->query("UPDATE fz_users SET name_surname='$name_surname', birth_date='$birthday', localization='$city' WHERE id='$user_id'"))
  {
    $_SESSION["upload-error"] = "Zaktualizowano dane.";
    ?> <script type="text/javascript">location.href = '../edit-profile.php';</script> <?php
    die();
  }
  else
  {
    $_SESSION["upload-error"] = "Wystąpił błąd przy wysyłaniu danych.";
    ?> <script type="text/javascript">location.href = '../edit-profile.php';</script> <?php
    die();
  }
?>
