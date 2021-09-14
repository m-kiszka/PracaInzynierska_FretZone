<?php
session_start();
require_once('./scriptConnect.php');
require_once('./scriptCheck_LoggedIn_Script.php');

if(isset($_POST["ar_description"]) && isset($_POST["report_type"]))
{
  $desc = $_POST["ar_description"];
  $report_type = $_POST["report_type"];
  $report_url = $_SERVER['HTTP_REFERER'];
  $userid = $_SESSION["userid"];

  $uploadOK = true;

  if(!is_null($desc) && strlen($desc)>0)
  {
    if(strlen($desc)>1000)
    {
      $_SESSION["add_report_error"] = "Treść nie może zawierać więcej niż 1000 znaków.";
      $uploadOK = false;
    }
    else
    {
      $desc = htmlspecialchars($desc);
    }
  }
  else
  {
    $_SESSION["add_report_error"] = "Treść nie może być pusta.";
    $uploadOK = false;
  }

  $result = $connect->query("SELECT * FROM fz_reports WHERE url='$report_url' AND user_id='$userid' LIMIT 1");
  $num_rows = $result->num_rows;

  if($num_rows > 0)
  {
    $_SESSION["add_report_error"] = "Przyjęliśmy twoje zgłoszenie.";
    $uploadOK = false;
  }

  if($uploadOK)
  {
    if($connect->query("INSERT INTO fz_reports (report_type, description, added_date, user_id, type, url, status) VALUES ('$report_type', '$desc', CURRENT_TIMESTAMP, '$userid', 'TEST', '$report_url', 'Otwarty')"))
    {
      $_SESSION["add_report_error"] = "Przyjęliśmy twoje zgłoszenie.";
      ?><script type="text/javascript">location.href = "<?php echo $report_url; ?>";</script><?php
      die();
    }
    else
    {
      $_SESSION["add_report_error"] = "Wystąpił błąd przy wysyłaniu zgłoszenia.";
      ?><script type="text/javascript">location.href = "<?php echo $report_url; ?>";</script><?php
      die();
    }
  }
  else
  {
    ?><script type="text/javascript">location.href = "<?php echo $report_url; ?>";</script><?php
    die();
  }
}
else
{
  $_SESSION["add_report_error"] = "Wypełnij wszystkie obowiązkowe pola.";
  ?><script type="text/javascript">location.href = "<?php echo $report_url; ?>";</script><?php
  die();
}
?>
