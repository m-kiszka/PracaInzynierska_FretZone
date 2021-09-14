<?php
session_start();
$login = $password = $confirm_password = $email = $confirm_email = "";
require_once('./scriptConnect.php');
if(isset($_SESSION["register_error"]))
{
  unset($_SESSION["register_error"]);
}

if(isset($_SESSION["userid"]))
{
  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
}

$registerOK=true;

$login = $_POST["login"];
$email = $_POST["email"];
$confirm_email = $_POST["confirm_email"];

//REJESTRACJA
if (!is_null($login) && !is_null($email) && !is_null($confirm_email))
{
  //sprawdzanie czy uzytkownik wypelnil wszystkie pola
  if($login!="" && $email!="" && $confirm_email!="")
  {
    if($email!=$confirm_email)
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Podane e-maile nie są identyczne.";
    }

    if(strlen($email)>320)
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Podany adres e-mail jest zbyt długi.";
    }

    if(filter_var($email, FILTER_VALIDATE_EMAIL)==false)
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Podany adres e-mail jest nieprawidłowy.";
    }

    //sprawdzanie czy login ma za mało lub za dużo znaków
    if(strlen($login)>64 || strlen($login)<3)
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Nazwa użytkownika nie może być krótsza niż 3 znaki i dłuzsza niż 64 znaki.";
    }

    //sprawdzanie czy login nie zawiera niedozwolonych slow
    $forbidden = array("admin","test","freztone","me","my");
    if(in_array($login, $forbidden))
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Login zawiera jedno z zabronionych słów.";
    }

    //sprawdzanie czy login zawiera nieodpowiednie znaki
    if (preg_match("/[^A-Za-z0-9_-]/", $login))
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Login może zawierać tylko litery, cyfry oraz znak \"-\" i \"_\".";
    }

    if($registerOK==true)
    {
      $login = przetworz_dane($login);
      $email = przetworz_dane($email);

      //jesli login istnieje juz w bazie
      $result = $connect->query("SELECT * FROM fz_users WHERE login = '$login'");
      $result = $result->fetch_row();

      if($result!=null)
      {
        $registerOK=false;
        $_SESSION["register_error"] = "Podane konto już istnieje.";
      }

      //jesli email istnieje juz w bazie
      $result = $connect->query("SELECT * FROM fz_users WHERE email = '$email'");
      $result = $result->fetch_row();

      if($result!=null)
      {
        $registerOK=false;
        $_SESSION["register_error"] = "Podane konto już istnieje.";
      }
    }
    else
    {
      $registerOK=false;
    }
  }
  else
  {
    $registerOK=false;
    $_SESSION["register_error"] = "Wypełnij wszystkie pola.";
  }

  if(!isset($_POST["terms"]))
  {
    $registerOK=false;
    $_SESSION["register_error"] = "Aby utworzyć konto należy zaakceptować regulamin.";
  }
}
else
{
  $registerOK=false;
  $_SESSION["register_error"] = "Wystąpił błąd przy przesyłaniu danych. Spróbuj ponownie później.";
}

//wysylanie danych do bazy, tworzenie uzytkownika
if($registerOK==true)
{
  $current_date=date("Y-m-d");

  $generated_ID = $_SESSION["temp_id"];
  unset($_SESSION["temp_id"]);

  //user_rank 1 == zweryfikowany e-mail
  if($connect->query("INSERT INTO fz_users (id, login, email, account_creation_date, OAUTH, user_rank) VALUES ('$generated_ID', '$login', '$email', CURRENT_TIMESTAMP, 'Google', 1)"))
  {
    $_SESSION["register_error"] = "Zarejestrowano użytkownika poprawnie.";
    ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
    die();
  }
  else
  {
    ?> <script type="text/javascript">location.href = '../register-google.php';</script> <?php
    die();
  }
}
else
{
  ?> <script type="text/javascript">location.href = '../register-google.php';</script> <?php
  die();
}

function przetworz_dane($dane)
{
  $dane = trim($dane); //usuwa biale znaki
  $dane = stripslashes($dane); //usuwa cudzyslow
  $dane = htmlspecialchars($dane); //zamienia poszczegolne znaki na odwolania znakowe, co uniemozliwia wywolanie kodu html w danym polu
  return $dane;
}
?>
