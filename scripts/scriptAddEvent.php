<?php
session_start();
require_once('./scriptConnect.php');
require_once('./scriptCheck_LoggedIn_Script.php');

$uploadOK = true;
if(isset($_POST["ae_title"]) && isset($_POST["ae_description"]) && isset($_POST["ae_startEvent"]) && isset($_POST["ae_endEvent"]))
{
  $title = $_POST["ae_title"];
  $desc = $_POST["ae_description"];
  $start_date = $_POST["ae_startEvent"];
  $end_date = $_POST["ae_endEvent"];
  $tags = $_POST["tags"];

  if(isset($_POST["ae_tabLink"]))
  {
    $tab_link = $_POST["ae_tabLink"];
    $tab_link = urlencode($tab_link);
  }
  else
  {
    $tab_link="";
  }

  if(!is_null($title) && $title!="")
  {
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
  }
  else
  {
    $_SESSION["add_event_error"] = "Opis nie może być pusty.";
    $uploadOK = false;
  }

  if(!is_null($tags) && $tags!="")
  {
    $tags=strtolower($tags);
    $tags=str_replace(" ","",$tags);
    $tags=trim($tags);

    if(strlen($tags)>128)
    {
      $_SESSION["add_event_error"] = "Pole na tagi nie może zawierać więcej niż 128 znaków.";
      $uploadOK = false;
    }
    if(substr_count($tags,',')>5)
    {
      $_SESSION["add_event_error"] = "Nie można dodać więcej niż 6 tagów.";
      $uploadOK = false;
    }
    if (!preg_match("/^[A-Za-z0-9,-]+$/", $tags))
    {
      $_SESSION["add_event_error"] = "Ciąg słów należy oddzielać znakiem \"-\", a tagi znakiem \",\".";
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

  if(!isset($start_date) || !isset($end_date))
  {
    $_SESSION["add_event_error"] = "Wybierz datę rozpoczęcia i zakończenia wydarzenia.";
    $uploadOK = false;
  }
  else
  {
    if($start_date>=$end_date)
    {
      $_SESSION["add_event_error"] = "Wydarzenie musi trwać minimum jeden dzień.";
      $uploadOK = false;
    }
    if($start_date<date("Y-m-d"))
    {
      $_SESSION["add_event_error"] = "Wydarzenie musi rozpocząć się najwcześniej dzisiaj.";
      $uploadOK = false;
    }
  }

  if($uploadOK)
  {
    if(date("Y-m-d")==$start_date)
    {
      $status="ACTIVE";
    }
    elseif(date("Y-m-d")<$start_date)
    {
      $status="UPCOMING";
    }
  }

  if($uploadOK)
  {
    if($connect->query("INSERT INTO fz_chall (name, description, tab_url, start_date, end_date, user_id, status) VALUES ('$title', '$desc', '$tab_link', '$start_date', '$end_date', '$temp_session', '$status')"))
    {
      //sprawdzanie id dodanego wydarzenia
      $chall_id = $connect->insert_id;

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
            $connect->query("INSERT INTO fz_chall_tags (chall_id, tag_id) VALUES ('$chall_id','$tag_id[0]')");
          }
          //jeśli tag nie istnieje, to dodanie go do bazy i potem przypięcie
          else
          {
            $connect->query("INSERT INTO fz_tags (name) VALUES ('$tag')");
            $tag_id_in_db = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
            $tag_id_in_db = $tag_id_in_db->fetch_row();

            foreach($tag_id_in_db as $tag_id)
            {
              $connect->query("INSERT INTO fz_chall_tags (chall_id, tag_id) VALUES ('$chall_id','$tag_id')");
            }
          }
        }
      }
      echo "<script>location.href=\"../event.php?v=$chall_id\";</script>";
      die();
    }
    else
    {
      $_SESSION["add_event_error"] = "Wystąpił błąd przy tworzeniu wydarzenia.";
      ?><script type="text/javascript">location.href = '../add-event.php';</script><?php
      die();
    }
  }
  else
  {
    ?><script type="text/javascript">location.href = '../add-event.php';</script><?php
    die();
  }
}
else
{
  $_SESSION["add_event_error"] = "Wypełnij wszystkie obowiązkowe pola.";
  ?><script type="text/javascript">location.href = '../add-event.php';</script><?php
  die();
}
?>
