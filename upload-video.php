<?php session_start(); session_regenerate_id(true); require_once('./scripts/scriptConnect.php'); require_once('./scripts/scriptCheck_LoggedIn.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <script src="./resources/jquery-3.5.1.min.js"></script>
    <title>Prześlij wykonanie - FretZone</title>
  </head>
  <body>
    <?php
    include('./scripts/scriptCookiesBanner.html');

    if(isset($_GET["e"]) && $_GET["e"]==0)
    {
      unset($_SESSION["chall_id"]);
    }
    ?>
    <!-- banner -->
    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text">Dodaj wykonanie</p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <div class="el-container">
      <div id="loading">
        <p>Trwa wysyłanie...</br></br>Nie opuszczaj strony do jego zakończenia.</p>
      </div>
      <div class="upload-inputs">
        <form action="./scripts/scriptUploadVideo.php" class="form" method="post" enctype="multipart/form-data">
          <label>Plik</label>
          <label class="switch">
            <input type="checkbox" class="switchbox" name="switch_type" value="file">
            <span class="slider Round"></span>
          </label>
          <label>YouTube Link</label><br><br>

          <script type="text/javascript">
            function clear_post()
            {
              location.href="./upload-video.php?e=0";
            }
          </script>

          <?php
          if((isset($_POST["add_to_event"]) && $_POST["add_to_event"]!="") || isset($_SESSION["chall_id"]))
          {
            if(isset($_POST["add_to_event"]))
            {
              $event_id = $_POST["add_to_event"];
            }
            else
            {
              $event_id = $_SESSION["chall_id"];
              unset($_SESSION["chall_id"]);
            }

            $result = $connect->query("SELECT id, name, start_date, end_date FROM fz_chall WHERE id='$event_id' AND CURRENT_DATE() between start_date and end_date");
            if($result!=null)
            {
              $result = $result->fetch_row();
              $event[0]=$result[0];
              $event[1]=$result[1];
              $event[2]=$result[2];
              $event[3]=$result[3];
            }
          }
          ?>

          <!-- chowanie przy switchu -->
          <span class="youtube_link">
            <label>Prześlij nagranie (może być też link skrócony youtu.be)*</label><br>
            <input type="text" class="ep-input" name="youtubeLink" class="input_youtube_link" placeholder="np. https://www.youtube.com/watch?v=dQw4w9WgXcQ"><br><br>
          </span>

          <!-- chowanie przy switchu -->
          <span class="file_link">
            <label>Prześlij plik w formacie .mp4*</label><br>
            <input type="file" name="fileToUpload" id="fileToUpload" class="input_file_link"><br><br>
          </span>
          <label>Nazwa*</label><br>
          <input type="text" class="ep-input" name="name" placeholder="np. Iron Maiden - Fear of the Dark cover"><br>
          <label>Opis</label><br>
          <textarea name="description" class="at-textarea" rows="8" cols="50"></textarea><br><br>
          <label>Tagi</label><br>
          <input type="text" class="ep-input" name="tags" placeholder="Max. 6, słowa oddzielaj ' - ', a tagi ' , '"><br>

          <label>Pola oznaczone gwiazdką (*) są obowiązkowe.<label><br>

          <input type="submit" id="zatwierdz-wideo" class=" ep-submit buttonSend" name="submitVideo" value="Prześlij"> <br><br> <!-- zielony -->

        <?php if(isset($event) && $event[0]!=null)
        {
        $_SESSION["chall_id"]=$event[0]; ?>

        <span>Zgłoszenie dla:</span>
                       <label> <?php echo "<a href=\"./event.php?v=".$event[0]."\"> ".$event[1]."</a>"; ?> </label>
                        <label> <?php echo $event[2]; ?> </label>
                        <label> <?php echo $event[3]; ?> </label><br>

        <input type="button" class=" ep-submit buttonCancel" value="Nie zgłaszaj" onclick="clear_post()">
        <?php } ?>
        </form>
      </div>
      <nav class="nav-bottom">
        <a href="./scripts/scriptLogout.php" class="nav-item">
        <i class='icon icon-logout fas fa-power-off'></i>
          <span class="menu-text">Wyloguj</span>
        </a>
        <a href="./edit-account.php" class="nav-item">
        <i class='icon fas fa-cog'></i>
          <span class="menu-text">Ustawienia</span>
        </a>
        <a href="./profile.php" class="nav-item">
        <i class='icon fas fa-user-alt'></i>
          <span class="menu-text">Mój profil</span>
        </a>
      <!-- <a href="./moderator.php"><i class='icon fas fa-tachometer-alt'></i></a> -->
    </nav>
    </div>
    <!-- Komunikat o błędzie -->
    <?php
    if(isset($_SESSION["upload_error"])) {
       ?><script>alert("<?php echo $_SESSION["upload_error"];?>");</script> <?php
       unset($_SESSION["upload_error"]);
     } ?>
     <footer>
      <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
     </footer>

     <!-- przelacznik YT/mp4 -->
     <script>
     $(document).ready(function()
     {
       $('#loading').hide();
       $('.youtube_link').hide();
       $('.file_link').show();
       $('.switchbox').prop('checked',false);

       $('.switchbox').change(function() {
         if($('.switchbox').is(":checked"))
         {
           $('.youtube_link').show();
           $(".file_link").hide();
           $(".input_file_link").val(null);
         }
         else
         {
           $('.youtube_link').hide();
           $('.file_link').show();
           $(".input_youtube_link").val(null);
         }
       });
     });
     </script>

     <script>
     $(document).ready(function()
     {
       $('#zatwierdz-wideo').click(function()
       {
         $('#loading').show();
         $('.upload-inputs').hide();
       });
     });
     </script>
  </body>
</html>
