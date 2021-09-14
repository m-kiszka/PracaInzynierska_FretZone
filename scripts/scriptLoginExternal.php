<?php session_start(); require_once('./scriptConnect.php');
if(isset($_SESSION["userid"]))
{
  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
}

if(isset($_SESSION["temp_id"]))
{
  $id = $_SESSION["temp_id"];
  $result = $connect->query("SELECT id, login FROM fz_users WHERE id='$id'");
  $result = $result->fetch_row();
  if(isset($result) && $result[0]!="")
  {
    $_SESSION["userid"]=$result[0];
    $_SESSION["login"]=$result[1];
    unset($_SESSION["temp_id"]);
    unset($_SESSION["login_error"]);
    ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
    die();
  }
  else
  {
    $_SESSION["login_error"] = "Wystąpił błąd przy przesyłaniu danych. Spróbuj ponownie później.";
    ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
    die();
  }
}
else
{
  $_SESSION["login_error"] = "Wystąpił błąd.";
  ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
  die();
}
