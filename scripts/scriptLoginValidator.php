<?php
session_start();
$login = $password = "";
require_once('./scriptConnect.php');
if(isset($_SESSION["login_error"]))
{
  unset($_SESSION["login_error"]);
}

if(isset($_SESSION["userid"]))
{
  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
}

//LOGOWANIE
if (!is_null($login) && !is_null($password))
{
  $login = $_POST["login"];
  $password = $_POST["password"];
  //$password = password_hash($password, PASSWORD_DEFAULT);
  if($login!="" && $password!="")
  {
    $login = process_data($login);
    $password = process_data($password);

    $result = $connect->query("SELECT id, login, password, user_rank, verify_code, email FROM fz_users WHERE login = '$login'");
    $result = $result->fetch_row();

    if($result!=null)
    {
      //echo "Login istnieje w bazie.";
      //sprawdzanie czy konto posiada ustawione hasło
      if($result[2]!=null)
      {
        //sprawdzanie czy haslo sie zgadza
        if(password_verify($password, $result[2]))
        {
        //sprawdzanie czy użytkownik zweryfikował e-mail
          if($result[3]>0)
          {
            session_gc();
            session_regenerate_id(true);
            $_SESSION["userid"]=$result[0];
            $_SESSION["login"]=$result[1];
            unset($_SESSION["login_error"]);
            ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
            die();
          }
          elseif($result[3]==0)
          {
            //$mail_msg = "Wejdź na podany adres, aby aktywować swoje konto: \n\nhttp://fretzone.westeurope.cloudapp.azure.com/verify-account.php?v=".$result[4];
            $mail_msg = "Wejdź na podany adres, aby aktywować swoje konto: \n\nhttps://www.fretzone.pl/verify-account.php?v=".$result[4];

            $mail_headers = "From: welcome@fretzone.pl\r\n";
            $mail_headers .= "MIME-Version: 1.0\r\n";
            $mail_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $mail_headers .= "X-Priority: 1\r\n";

            mail($result[5], "Aktywuj konto - FretZone", $mail_msg, $mail_headers);

            $_SESSION["login_error"] = "Konto nie zostało jeszcze zweryfikowane. Na e-mail podany przy rejestracji została ponownie wysłana wiadomość e-mail z linkiem do weryfikacji.";
            ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
            die();
          }
          elseif($result[3]==-1)
          {
            $_SESSION["login_error"] = "To konto zostało zawieszone.";
            ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
            die();
          }
        }
        else
        {
          $_SESSION["login_error"] = "Błędne dane.";
        }
      }
      else
      {
        $_SESSION["login_error"] = "To konto używa innego sposobu logowania.";
      }
    }
    else
    {
        $_SESSION["login_error"] = "Błędne dane.";
    }
  }
  else
  {
    $_SESSION["login_error"] = "Wypełnij wszystkie pola.";
  }
}
else
{
  $_SESSION["login_error"] = "Wystąpił błąd przy przesyłaniu danych. Spróbuj ponownie później.";
}

?> <script type="text/javascript">location.href = '../login.php';</script> <?php
die();

function process_data($dane)
{
  $dane = trim($dane); //usuwa biale znaki
  $dane = stripslashes($dane); //usuwa cudzyslow
  $dane = htmlspecialchars($dane); //zamienia poszczegolne znaki na odwolania znakowe, co uniemozliwia wywolanie kodu html w danym polu
  return $dane;
}
?>
