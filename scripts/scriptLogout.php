<?php
  session_start();

  $_SESSION = array();

  if(ini_get("session.use_cookies")) //jeśli sesja używa ciasteczek, to je również kasujemy
  {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
  }

  session_destroy();
  session_write_close();

  ?> <script type="text/javascript">location.href = '../index.php';</script> <?php
  die();
?>
