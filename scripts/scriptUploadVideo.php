<?php
session_start();
require_once('./scriptConnect.php');
require_once('./scriptFtpData.php');
require_once('./scriptCheck_LoggedIn_Script.php');

$uploadOK = true;
$directory = "..\uploads\\videos\\";

$name = $_POST["name"];
$desc = $_POST["description"];
$tags = $_POST["tags"];

if(!isset($_SESSION["userid"]))
{
  ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
  die();
}
else
{
  $temp_session=$_SESSION["userid"];
  $temp = $connect->query("SELECT user_rank FROM fz_users WHERE id='$temp_session'");
  $temp = $temp->fetch_row();
  if($temp[0]==-1)
  {
    ?> <script type="text/javascript">location.href = './scriptLogout.php';</script> <?php
    die();
  }
}

$login = $_SESSION["login"];
$userid = $_SESSION["userid"];
$video_type = 0;

if(is_null($name) || $name=="")
{
  $_SESSION["upload_error"]="Wypełnij wszystkie obowiązkowe pola.";
  $uploadOK = false;
}
else
{
  if(strlen($name)<3 && strlen($name)>128)
  {
    $_SESSION["upload_error"] = "Nazwa musi zawierać więcej niż 3 znaki i mniej niż 128 znaków.";
    $uploadOK = false;
  }

  if (preg_match("/[^A-Za-z0-9ąĄęĘłŁóÓżŻśŚćĆńŃ_'.!?&()+: -]/", $name))
  {
    $_SESSION["upload_error"] = "Nazwa zawiera niedozwolone znaki.";
    $uploadOK=false;
  }

  if(!is_null($tags) && $tags!="")
  {
    $tags=strtolower($tags);
    $tags=str_replace(' ', '', $tags);
    $tags=trim($tags);
    
    if(strlen($tags)>128)
    {
      $_SESSION["upload_error"] = "Pole na tagi nie może zawierać więcej niż 128 znaków.";
      $uploadOK = false;
    }
    if(substr_count($tags,',')>5)
    {
      $_SESSION["upload_error"] = "Nie można dodać więcej niż 6 tagów.";
      $uploadOK = false;
    }
    if (!preg_match("/^[A-Za-z0-9,-]+$/", $tags))
    {
      $_SESSION["upload_error"] = "Ciąg słów należy oddzielać znakiem \"-\", a tagi znakiem \",\".";
      $uploadOK=false;
    }
  }
  else
  {
    $tags="";
  }

  if(!is_null($desc))
  {
    if(strlen($desc)>512)
    {
      $_SESSION["upload_error"] = "Opis nie może zawierać więcej niż 512 znaków.";
      $uploadOK = false;
    }
    if (preg_match("/[^A-Za-z0-9ąĄęĘłŁóÓżŻśŚćĆńŃ_'.!?&()+: -]/", $name))
    {
      $_SESSION["upload_error"] = "Opis zawiera niedozwolone znaki.";
      $uploadOK = false;
    }
  }
  else
  {
    $desc = "";
  }
}

if(isset($_POST["switch_type"])) //youtube
{
  $url = $_POST["youtubeLink"];

  $video_type = 1;
}
else //plik mp4
{
  $video_type = 0;
  if($_FILES["fileToUpload"]["tmp_name"]==null)
  {
    $_SESSION["upload_error"] = "Podany plik jest nieprawidłowy.";
    ?><script type="text/javascript">location.href = '../upload-video.php';</script><?php
    die();
  }
}

//czy ostatecznie mozna wyslac plik na serwer
if (!$uploadOK)
{
  ?><script type="text/javascript">location.href = '../upload-video.php';</script><?php
  die();
}
//ostateczny upload pliku
else
{
  if($video_type==0) //plik mp4
  {
    $file = $_FILES["fileToUpload"]["tmp_name"];

    $fileDim = getimagesize($file);

    //sprawdzanie rozmiaru pliku, max 300MB
    if ($_FILES["fileToUpload"]["size"] > 300000000)
    {
      $uploadOK = false;
      $_SESSION["upload_error"] = "Rozmiar pliku jest zbyt duży.";
    }

    //format pliku
    $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
    //$fileType = $fileDim[2];

    //sprawdzanie formatu pliku
    if($fileType != "mp4")
    {
      $uploadOK = false;
      $_SESSION["upload_error"] = "Nieprawidłowy format pliku.";
    }

    //MOVE UPLOADED
    /*
    $result = $connect->query("SELECT id FROM fz_videos ORDER BY id LIMIT 1");
    $result = $result->fetch_row();
    $result = $result[0]+1;

    $url = $login."_".$result.".mp4";
    if(move_uploaded_file($file, $directory.$login."_".$result.".mp4") && $connect->query("INSERT INTO fz_videos (name, description, url, added_date, user_id, type) VALUES ('$name','$desc','$url',CURRENT_TIMESTAMP, '$userid', 0)"))
    {
      //sprawdzanie id dodanego wykonania
      $performance_id = $connect->insert_id;

      if(isset($_SESSION["chall_id"]))
      {
        $chall_id = $_SESSION["chall_id"];
      }
      else
      {
        $chall_id="";
      }

      //dodawanie do wykonania id wydarzenia, jeśli istnieje
      if($chall_id!="")
      {
        if($connect->query("UPDATE fz_videos SET chall_id='$chall_id' WHERE id='$performance_id'"))
        {
          unset($_SESSION["chall_id"]);
        }
      }

      //dodanie tagów do odpowiedniej tabeli
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

          if($tag_id[0]!=null)
          {
            $connect->query("INSERT INTO fz_videos_tags (performance_id, tag_id) VALUES ('$performance_id','$tag_id[0]')");
            echo mysqli_error($connect);
          }
          //jeśli tag nie istnieje, to dodanie go do bazy i potem przypięcie
          else
          {
            $connect->query("INSERT INTO fz_tags (name) VALUES ('$tag')");
            $tag_id_in_db = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
            $tag_id_in_db = $tag_id_in_db->fetch_row();

            foreach($tag_id_in_db as $tag_id)
            {
              $connect->query("INSERT INTO fz_videos_tags (performance_id, tag_id) VALUES ('$performance_id','$tag_id')");
            }
            echo mysqli_error($connect);
          }
        }
      }
      echo "<script>location.href=\"../play.php?v=$performance_id\";</script>";
    }
    else
    {
      $_SESSION["upload-error"] = "Wystąpił błąd przy przesyłaniu pliku.";
      echo "Wystąpił błąd przy przesyłaniu pliku.";
      ?><script type="text/javascript">location.href = '../upload-video.php';</script><?php
    }
    */
    //MOVE UPLOADED


    //FTP
    $result = $connect->query("SELECT id FROM fz_videos ORDER BY id LIMIT 1");
    $result = $result->fetch_row();
    if($result!=null)
    {
      $result = $result[0]+1;
    }
    else
    {
      $result = "0";
    }

    $url = $login."_".$result.".mp4";

    if(ftp_put($ftp_conn, '//PracaInz_Final/uploads/videos/'.$url, $file, FTP_BINARY) && $connect->query("INSERT INTO fz_videos (name, description, url, added_date, user_id, type, status) VALUES ('$name','$desc','$url', CURRENT_TIMESTAMP, '$userid', 0, 'OK')"))
    {
      //sprawdzanie id dodanego wykonania
      $performance_id = $connect->insert_id;

      if(isset($_SESSION["chall_id"]))
      {
        $chall_id = $_SESSION["chall_id"];
      }
      else
      {
        $chall_id="";
      }

      //dodawanie do wykonania id wydarzenia, jeśli istnieje
      if($chall_id!="")
      {
        if($connect->query("UPDATE fz_videos SET chall_id='$chall_id' WHERE id='$performance_id'"))
        {
          unset($_SESSION["chall_id"]);
        }
      }

      //dodanie tagów do odpowiedniej tabeli
      if($tags!="")
      {
        //rozbicie tagów do tablicy, jeśli zostały wpisane
        $tags_array = explode(",",$tags);
        //przypięcie tagów do wykonania
        foreach($tags_array as $tag)
        {
          //sprawdzanie czy tag istnieje w bazie
          $tag_id = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
          $tag_id = $tag_id->fetch_row();

          if($tag_id[0]!=null)
          {
            $connect->query("INSERT INTO fz_videos_tags (performance_id, tag_id) VALUES ('$performance_id','$tag_id[0]')");
          }
          //jeśli tag nie istnieje, to dodanie go do bazy i potem przypięcie
          else
          {
            $connect->query("INSERT INTO fz_tags (name) VALUES ('$tag')");
            $tag_id_in_db = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
            $tag_id_in_db = $tag_id_in_db->fetch_row();

            foreach($tag_id_in_db as $tag_id)
            {
              $connect->query("INSERT INTO fz_videos_tags (performance_id, tag_id) VALUES ('$performance_id','$tag_id')");
            }
            echo mysqli_error($connect);
          }
        }
      }
      echo "<script>location.href=\"../play.php?v=".$performance_id."\";</script>";
      die();
    }
    else
    {
      $_SESSION["upload-error"] = "Wystąpił błąd przy przesyłaniu pliku.";
      ?><script type="text/javascript">location.href = '../upload-video.php';</script><?php
      die();
    }
    ftp_close($ftp_conn);
  }
  elseif($video_type==1) //youtube
  {
    $result = $connect->query("SELECT id FROM fz_videos ORDER BY id LIMIT 1");
    $result = $result->fetch_row();
    $result = $result[0]+1;

    $url = $_POST["youtubeLink"];
    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $url);

    if($connect->query("INSERT INTO fz_videos (name, description, url, added_date, user_id, type) VALUES ('$name','$desc','$url[0]',CURRENT_TIMESTAMP, '$userid', 1)"))
    {
      //sprawdzanie id dodanego wykonania
      $performance_id = $connect->insert_id;

      if(isset($_SESSION["chall_id"]))
      {
        $chall_id = $_SESSION["chall_id"];
      }
      else
      {
        $chall_id="";
      }

      //dodawanie do wykonania id wydarzenia, jeśli istnieje
      if($chall_id!="")
      {
        if($connect->query("UPDATE fz_videos SET chall_id='$chall_id' WHERE id='$performance_id'"))
        {
          unset($_SESSION["chall_id"]);
        }
      }

      //dodanie tagów do odpowiedniej tabeli
      if($tags!="")
      {
        //rozbicie tagów do tablicy, jeśli zostały wpisane
        $tags_array = explode(",",$tags);
        //przypięcie tagów do wykonania
        foreach($tags_array as $tag)
        {
          //sprawdzanie czy tag istnieje w bazie
          $tag_id = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
          $tag_id = $tag_id->fetch_row();

          if($tag_id[0]!=null)
          {
            $connect->query("INSERT INTO fz_videos_tags (performance_id, tag_id) VALUES ('$performance_id','$tag_id[0]')");
          }
          //jeśli tag nie istnieje, to dodanie go do bazy i potem przypięcie
          else
          {
            $connect->query("INSERT INTO fz_tags (name) VALUES ('$tag')");
            $tag_id_in_db = $connect->query("SELECT id FROM fz_tags WHERE name='$tag'");
            $tag_id_in_db = $tag_id_in_db->fetch_row();

            foreach($tag_id_in_db as $tag_id)
            {
              $connect->query("INSERT INTO fz_videos_tags (performance_id, tag_id) VALUES ('$performance_id','$tag_id')");
            }
          }
        }
      }
      echo "<script>location.href=\"../play.php?v=".$performance_id."\";</script>";
      die();
    }
    else
    {
      $_SESSION["upload-error"] = "Wystąpił błąd przy przesyłaniu pliku.";
      ?><script type="text/javascript">location.href = '../upload-video.php';</script><?php
      die();
    }
  }
}
?>
