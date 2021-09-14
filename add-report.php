<?php require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Wyślij zgłoszenie - FretZone</title>
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
  </head>
  <body>
    <div class="container">
    <center>  <h1>Zgłoszenie</h1>
      <form class="report" class="ar_report" action="./scripts/scriptAddReport.php" method="post">
        <input type="textarea" disabled id="report_url">
          <script>
            window.onload = function()
            {
              document.getElementById('report_url').value = window.location.href;
            }
          </script>
        </input>
        <label>Typ zgłoszenia*</label>
        <select name="report_type" id="report_type" class="report-type-select">
          <option>Spam</option>
          <option>Groźby</option>
          <option>Nieprzyzwoita treść</option>
          <option>Nieprawidłowości w działaniu strony</option>
          <option>Pytanie</option>
        </select><br><br>
        <label>Treść*</label><br>
        <textarea name="ar_description" class="ar-description" rows="10" cols="80"></textarea><br><br>
        <p style="color:red;">Wysyłając zgłoszenie, oświadczam, iż treść jego jest zgodna z prawdą.</p><br>

        <label>Pola oznaczone gwiazdką (*) są obowiązkowe.<label><br><br>

        <input type="submit" name="ar_submit" class="ar_submit" value="Wyślij zgłoszenie">
      </form>
    </center>
    </div>
  </body>
</html>
