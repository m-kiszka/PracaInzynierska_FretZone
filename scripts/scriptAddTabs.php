<?php
session_start();
require_once('./scriptConnect.php');
require_once('./scriptCheck_LoggedIn_Script.php');

$uploadOK = true;
if(isset($_POST["tabsTitle"]) && isset($_POST["tabsDesc"]) && isset($_POST["tabsTextArea"]) && isset($_POST["tabsOwner"]))
{
  $title = $_POST["tabsTitle"];
  $desc = $_POST["tabsDesc"];
  $tags = $_POST["tags"];
  $tab = $_POST["tabsTextArea"];

  if(!is_null($title) && $title!="")
  {
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
  }
  else
  {
    $_SESSION["add_tabs_error"] = "Nazwa nie może być pusta.";
    $uploadOK=false;
  }

  if(!is_null($tags) && $tags!="")
  {
    $tags=strtolower($tags);
    $tags=str_replace(" ","",$tags);
    $tags=trim($tags);

    if(strlen($tags)>128)
    {
      $_SESSION["add_tabs_error"] = "Pole na tagi nie może zawierać więcej niż 128 znaków.";
      $uploadOK = false;
    }
    if(substr_count($tags,',')>5)
    {
      $_SESSION["add_tabs_error"] = "Nie można dodać więcej niż 6 tagów.";
      $uploadOK = false;
    }
    if (!preg_match("/^[A-Za-z0-9,-]+$/", $tags))
    {
      $_SESSION["add_tabs_error"] = "Ciąg słów należy oddzielać znakiem \"-\", a tagi znakiem \",\".";
      $uploadOK=false;
    }
  }
  else
  {
    $tags="";
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
    if($connect->query("INSERT INTO fz_tabs (name, description, tab, added_date, status, user_id) VALUES ('$title', '$desc', '$tab', CURRENT_TIMESTAMP, 'OK', '$temp_session')"))
    {
      //sprawdzanie id dodanej tabulatury
      $tab_id = $connect->insert_id;

      if($tags!="")
      {
        $tags = htmlspecialchars($tags);

        //rozbicie tagów do tablicy, jeśli zostały wpisane
        $tags_array = explode(",",$tags);
        //przypięcie tagów do wykonania
        foreach($tags_array as $tag)
        {
          //sprawdzanie czy tag istnieje w bazie
          $tag_id = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
          $tag_id = $tag_id->fetch_row();

          if(isset($tag_id) && $tag_id!=null && $tag_id[0]!=null)
          {
            $connect->query("INSERT INTO fz_tabs_tags (tab_id, tag_id) VALUES ('$tab_id','$tag_id[0]')");
          }
          //jeśli tag nie istnieje, to dodanie go do bazy i potem przypięcie
          else
          {
            $connect->query("INSERT INTO fz_tags (name) VALUES ('$tag')");
            $tag_id_in_db = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
            $tag_id_in_db = $tag_id_in_db->fetch_row();

            foreach($tag_id_in_db as $tag_id)
            {
              $connect->query("INSERT INTO fz_tabs_tags (tab_id, tag_id) VALUES ('$tab_id','$tag_id')");
            }
          }
        }
      }
      echo "<script>location.href=\"../tab.php?v=$tab_id\";</script>";
      die();
    }
    else
    {
      $_SESSION["add_tabs_error"] = "Wystąpił błąd przy tworzeniu wydarzenia.";
      $_SESSION["add_tab_temp"] = $tab;
      ?><script type="text/javascript">location.href = '../add-tabs.php';</script><?php
      die();
    }
  }
  else
  {
    $_SESSION["add_tab_temp"] = $tab;
    ?><script type="text/javascript">location.href = '../add-tabs.php';</script><?php
    die();
  }
}
else
{
  $_SESSION["add_tabs_error"] = "Wypełnij wszystkie obowiązkowe pola.";
  ?><script type="text/javascript">location.href = '../add-tabs.php';</script><?php
  die();
}
?>
