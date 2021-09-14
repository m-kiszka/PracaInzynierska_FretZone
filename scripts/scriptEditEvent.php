<?php
session_start();
require_once('./scriptConnect.php');
require_once('./scriptCheck_LoggedIn_Script.php');

$uploadOK = true;

if(isset($_POST["ae_title"]) && isset($_POST["ae_description"]) && isset($_SESSION["event_id"]) && isset($_SESSION["event_change"]))
{
  $event_id = $_SESSION["event_id"];
  $title = $_POST["ae_title"];
  $desc = $_POST["ae_description"];
  unset($_SESSION["event_change"]);

  if(isset($_POST["ae_tabLink"]))
  {
    $tab_link = $_POST["ae_tabLink"];
    $tab_link = urlencode($tab_link);
  }
  else
  {
    $tab_link="";
  }

  if(strlen($title)<3 && strlen($title)>128)
  {
    $_SESSION["add_event_error"] = "Nazwa musi zawierać więcej niż 3 znaki i mniej niż 128 znaków.";
    $uploadOK = false;
  }

  if (preg_match("/[^A-Za-z0-9ąĄęĘłŁóÓżŻśŚćĆńŃ_'.!?&()+: -]/", $title))
  {
    $_SESSION["add_event_error"] = "Nazwa zawiera niedozwolone znaki.";
    $uploadOK=false;
  }

  if(!is_null($desc) && $desc!="")
  {
    if(strlen($desc)>1000)
    {
      $_SESSION["add_event_error"] = "Opis nie może zawierać więcej niż 1000 znaków.";
      $uploadOK = false;
    }
    else
    {
      $desc = htmlspecialchars($desc);
    }
  }
  else
  {
    $_SESSION["add_event_error"] = "Opis nie może być pusty.";
    $uploadOK = false;
  }

  if($uploadOK)
  {
    if($connect->query("UPDATE fz_chall SET name='$title', description='$desc', tab_url='$tab_link' WHERE id='$event_id'"))
    {
      unset($_SESSION["event_id"]);
      echo "<script>location.href=\"../event.php?v=$event_id\";</script>";
      die();
    }
    else
    {
      $_SESSION["add_event_error"] = "Wystąpił błąd przy tworzeniu wydarzenia.";

    }
  }
  else
  {
    ?><script type="text/javascript">location.href = '../edit-event.php';</script><?php
    die();
  }
}
else
{
  $_SESSION["add_event_error"] = "Wypełnij wszystkie obowiązkowe pola.";
  ?><script type="text/javascript">location.href = '../edit-event.php';</script><?php
  die();
}
?>
