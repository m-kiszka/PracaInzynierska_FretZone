<?php
session_start();
require_once('./scriptConnect.php');
require_once('./scriptCheck_LoggedIn_Script.php');

$uploadOK = true;
if(isset($_POST["tabsTitle"]) && isset($_POST["tabsDesc"]) && isset($_POST["tabsTextArea"]) && isset($_POST["tabsOwner"]) && isset($_SESSION["tab_id"]) && isset($_SESSION["tab_change"]))
{
  $title = $_POST["tabsTitle"];
  $desc = $_POST["tabsDesc"];
  $tab = $_POST["tabsTextArea"];
  $tab_id = $_SESSION["tab_id"];
  unset($_SESSION["tab_change"]);

  if(strlen($title)<3 && strlen($title)>128)
  {
    $_SESSION["add_tabs_error"] = "Nazwa musi zawierać więcej niż 3 znaki i mniej niż 128 znaków.";
    $uploadOK = false;
  }

  if (preg_match("/[^A-Za-z0-9ąĄęĘłŁóÓżŻśŚćĆńŃ_'.!?&()+: -]/", $title))
  {
    $_SESSION["add_tabs_error"] = "Nazwa zawiera niedozwolone znaki.";
    $uploadOK=false;
  }

  if(!is_null($desc) && $desc!="")
  {
    if(strlen($desc)>1000)
    {
      $_SESSION["add_tabs_error"] = "Opis nie może zawierać więcej niż 1000 znaków.";
      $uploadOK = false;
    }
    else
    {
      $desc = htmlspecialchars($desc);
    }
  }
  else
  {
    $_SESSION["add_tabs_error"] = "Opis nie może być pusty.";
    $uploadOK = false;
  }

  if(!is_null($tab) && $tab!="")
  {
    if(strlen($tab)>10000)
    {
      $_SESSION["add_tabs_error"] = "Tabulatura nie może zawierać więcej nż 10000 znaków.";
      $uploadOK = false;
    }
    else
    {
      $tab = htmlspecialchars($tab);
    }
  }
  else
  {
    $_SESSION["add_tabs_error"] = "Tabulatura nie może być pusta.";
    $uploadOK = false;
  }

  if($uploadOK)
  {
    if($connect->query("UPDATE fz_tabs SET name='$title', description='$desc', tab='$tab' WHERE id='$tab_id'"))
    {
      unset($_SESSION["tab_id"]);
      echo "<script>location.href=\"../tab.php?v=$tab_id\";</script>";
      die();
    }
    else
    {
      $_SESSION["add_tabs_error"] = "Wystąpił błąd przy tworzeniu wydarzenia.";
      $_SESSION["add_tab_temp"] = $tab;
      ?><script type="text/javascript">location.href = '../edit-tab.php';</script><?php
      die();
    }
  }
  else
  {
    $_SESSION["add_tab_temp"] = $tab;
    ?><script type="text/javascript">location.href = '../edit-tab.php';</script><?php
    die();
  }
}
else
{
  $_SESSION["add_tabs_error"] = "Wypełnij wszystkie obowiązkowe pola.";
  ?><script type="text/javascript">location.href = '../edit-tab.php';</script><?php
  die();
}
?>
