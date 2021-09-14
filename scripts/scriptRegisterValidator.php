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
}

$registerOK=true;

$login = $_POST["login"];
$email = $_POST["email"];
$confirm_email = $_POST["confirm_email"];
$email=htmlspecialchars($email);
$confirm_email=htmlspecialchars($confirm_email);
$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

//REJESTRACJA
if (!is_null($login) && !is_null($password) && !is_null($confirm_password) && !is_null($email) && !is_null($confirm_email))
{
  //sprawdzanie czy uzytkownik wypelnil wszystkie pola
  if($login!="" && $password!="" && $email!="" && $confirm_email!="" && $confirm_password!="")
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

    if($password!=$confirm_password)
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Podane hasła nie są identyczne.";
    }

    //sprawdzanie czy login ma za mało lub za dużo znaków
    if(strlen($login)>64 || strlen($login)<3)
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Nazwa użytkownika nie może być krótsza niż 3 znaki i dłuzsza niż 64 znaki.";
    }

    //sprawdzanie czy login nie zawiera niedozwolonych slow
    $forbidden = array("admin","test","me","my","fretzone");
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

    //sprawdzanie czy haslo jest bezpieczne
    if(!preg_match("#[0-9]+#", $password))
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
    }

    if(!preg_match("#[a-z]+#", $password))
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
    }

    if(!preg_match("#[A-Z]+#", $password))
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
    }

    if(!preg_match("/[\'^£$%&*()}{@#~?><>,|=_+!-]/", $password))
    {
      $registerOK=false;
		  $_SESSION["register_error"] = "Hasło musi zawierać przynajmniej jedną małą i jedną wielką literę, cyfrę i znak specjalny.";
	  }

    //sprawdzanie czy hasło mieści się w limicie znaków
    if(strlen($password)<8 || strlen($password)>255)
    {
      $registerOK=false;
      $_SESSION["register_error"] = "Hasło nie może być krótsze niż 8 znaków.";
    }

    if($registerOK==true)
    {
      $login = process_data($login);
      $email = process_data($email);

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
  $password = process_data($password);
  $password = password_hash($password, PASSWORD_DEFAULT);

  $current_date=date("Y-m-d");

  $max_id=$connect->query("SELECT COUNT(id) FROM fz_users");
  $max_id = $max_id->fetch_row();
  $max_id = $max_id[0];
  $date_string = date('Ymd');
  $random_number=rand(0,1000);
  $generated_ID = "user".$date_string.$max_id.$random_number;

  $verify_code = md5($generated_ID.rand(0,1000));

  //WYSYLANIE EMAILA
  //$mail_msg = "Wejdź na podany adres, aby aktywować swoje konto: \n\nhttp://fretzone.westeurope.cloudapp.azure.com/verify-account.php?v=".$verify_code;
  $mail_msg = "Wejdź na podany adres, aby aktywować swoje konto: \n\nhttps://www.fretzone.pl/verify-account.php?v=".$verify_code;

  $mail_headers = "From: welcome@fretzone.pl\r\n";
  $mail_headers .= "MIME-Version: 1.0\r\n";
  $mail_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $mail_headers .= "X-Priority: 1\r\n";

  //user_rank 1 == zweryfikowany e-mail
  if($connect->query("INSERT INTO fz_users (id, login, email, password, account_creation_date, OAUTH, user_rank, verify_code) VALUES ('$generated_ID', '$login', '$email', '$password', CURRENT_TIMESTAMP, 'None', 0, '$verify_code')"))
  {
    mail($email, "Aktywuj konto - FretZone", $mail_msg, $mail_headers);
    $_SESSION["register_error"] = "Zarejestrowano użytkownika poprawnie. Użyj linku aktywacyjnego z wiadomości wysłanej na podany adres e-mail, aby zweryfikować konto.";
    ?> <script type="text/javascript">location.href = '../login.php';</script> <?php
    die();
  }
  else
  {
    $_SESSION["register_error"] = "Wystąpił błąd przy przesyłaniu danych. Spróbuj ponownie później.";
    ?> <script type="text/javascript">location.href = '../register.php';</script> <?php
    die();
  }
}
else
{
  ?> <script type="text/javascript">location.href = '../register.php';</script> <?php
  die();
}

function process_data($dane)
{
  $dane = trim($dane); //usuwa biale znaki
  $dane = stripslashes($dane); //usuwa cudzyslow
  $dane = htmlspecialchars($dane); //zamienia poszczegolne znaki na odwolania znakowe, co uniemozliwia wywolanie kodu html w danym polu
  return $dane;
}
?>
