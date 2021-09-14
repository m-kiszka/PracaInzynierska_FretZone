<?php
session_start();
$idtoken = $_REQUEST["idtoken"];
$CLIENT_ID="438338313435-im2gn4blhcs67olph99b74niaoj4mu0u.apps.googleusercontent.com";

require_once('../vendor/autoload.php');
require_once('./scriptConnect.php');

// Get $id_token via HTTPS POST.
$client = new Google_Client(['client_id' => $CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
$payload = $client->verifyIdToken($idtoken);
if ($payload && $payload['aud']==$CLIENT_ID)
{
  $userid = $payload['sub'];
  $result = $connect->query("SELECT id FROM fz_users WHERE id='$userid'");
  $result = $result->fetch_row();

  //użytkownik istnieje
  if(!is_null($result))
  {
    $_SESSION["temp_id"]=$result[0];
    echo 1;
  }
  //użytkownik nie isnieje w bazie
  else
  {
    $_SESSION["temp_id"]=$payload['sub'];
    echo 0;
  }
}
else
{
  echo "Token nie został zweryfikowany pomyślnie.";
}

?>
