<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <?php
    if(isset($_GET["v"]) && $_GET["v"]!="")
    {
      $event_id=$_GET["v"];
    }
    else
    {
      ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
      die();
    }

    if(isset($_SESSION["event_change"]))
    {
      unset($_SESSION["event_change"]);
    }

    $result = $connect->query("SELECT fz_chall.id, fz_chall.name, fz_chall.description, fz_chall.tab_url, fz_chall.start_date, fz_chall.end_date, fz_users.login, fz_chall.user_id, fz_chall.status, fz_users.avatar_url
                               FROM fz_chall
                               JOIN fz_users
                               ON fz_chall.user_id=fz_users.id
                               WHERE fz_chall.id='$event_id'");

    if($result!=null)
    {
      $result = $result->fetch_row();

      if($result[0]=="")
      {
        ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
        die();
      }
      elseif($result[8]=="LOCKED")
      {
        ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
        die();
      }

      $today = date("Y-m-d");

      if($result[8]=="UPCOMING" && $today>=$result[4] && $today<$result[5])
      {
        $change_status = $connect->query("UPDATE fz_chall SET status='ACTIVE' WHERE id='$result[0]'");
        $result[8]="ACTIVE";
      }

      //przypisywanie danych rekordu do innej tablicy
      for($i=0;$i<count($result);$i++)
      {
        if($result[$i]!=null)
        {
          $event[$i]=$result[$i];
        }
        else
        {
          $event[$i]="";
        }
      }

      //zbierz z bazy id tagów
      $temp_tags_id = $connect->query("SELECT tag_id FROM fz_chall_tags WHERE chall_id='$event_id'");

      $tags_id = array();
      $tags = array();
      //dodaj każdy tag (rekord z tabeli) do tablicy
      while($row = $temp_tags_id->fetch_assoc())
      {
        array_push($tags_id, $row["tag_id"]);
      }

      //dodaj do tablicy każdy tag, którego id znajduje się w innej tablicy
      foreach($tags_id as $tag)
      {
          $tag_result = $connect->query("SELECT name FROM fz_tags WHERE id='$tag'");
          $tag_result = $tag_result->fetch_row();
          array_push($tags, $tag_result[0]);
      }
    }
    else
    {
      ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
      die();
    }

    //zamiana bbcode na html
      $bbcode = array(
    		'~\[b\](.*?)\[/b\]~s', //pogrubienie
    		'~\[i\](.*?)\[/i\]~s', //kursywa
    		'~\[u\](.*?)\[/u\]~s', //podkreślenie
        '~(\r\n|\n|\r)~', //kolejna linia
    		'~\[quote\](.*?)\[/quote\]~s', //cytowanie
    		'~\[size=(.*?)\](.*?)\[/size\]~s', //rozmiar tekstu
    		'~\[color=(.*?)\](.*?)\[/color\]~s', //kolor tekstu
    		'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s', //obrazek
        '~\[url=https?.*?(?:[/?&](?:e|vi?|ci)(?:[/=]|%3D)|youtu\.be/|embed/|/user/[^/]+#p/(?:[^/]+/)+)([\w-]{10,12})].*?\[/url]~i', //osadzanie filmu youtube, działa na watch i embed, [url=link][/url]
        '~\[url]https?.*?(?:[/?&](?:e|vi?|ci)(?:[/=]|%3D)|youtu\.be/|embed/|/user/[^/]+#p/(?:[^/]+/)+)([\w-]{10,12}).*?\[/url]~i'
        //[url]link[/url]
    	);

    	//znaczniki html
    	$htmlcode = array(
    		'<b>$1</b>',
    		'<i>$1</i>',
    		'<span style="text-decoration:underline;">$1</span>',
        '<br />',
    		'<pre>$1</'.'pre>',
    		'<span style="font-size:$1px;">$2</span>',
    		'<span style="color:$1;">$2</span>',
    		'<img src="$1" alt="" />',
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
    	);

    	//ostateczna zamiana z użyciem preg_replace
      $event[2] = htmlspecialchars($event[2]);
      $temp_desc = $event[2];
    	$event[2] = preg_replace($bbcode,$htmlcode,$event[2]);
      $event[2] = str_replace("<?php","",$event[2]);
      $event[2] = str_replace("<?","",$event[2]);
      $event[2] = str_replace("?>","",$event[2]);
      $event[2] = str_replace("<script>","",$event[2]);
      $event[2] = str_replace("</script>","",$event[2]);

      $_SESSION["event_id"]=$event[0];
      $_SESSION["event_name"]=$event[1];
      $_SESSION["event_desc"]=$temp_desc;
      $_SESSION["event_tab"]=$event[3];
    ?>

<!-- wyświetlanie zawartości strony -->
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <title><?php echo $event[1]; ?> - FretZone</title>
  </head>
  <body>
    <?php include('./scripts/scriptCookiesBanner.html'); ?>

    <!-- modal -->
      <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
          <span class="close">&times;</span>
          <?php if(isset($_SESSION["userid"])) { include("add-report.php"); } else { echo "Musisz się zalogować, aby wysłać zgłoszenie."; } ?>
        </div>
      </div>

    <div class="banner">
      <a title="Strona główna" href="./index.php">
        <div class="logo"></div>
      </a>
        <p class="logo-text"><?php echo $event[1]; ?></p>
      <?php include('./scripts/scriptBannerButtons.php'); ?>
    </div>

    <!-- container -->
  <div class="ul-container">
      <h2 class="e-view x"><?php echo $event[1]; ?> <i class='fas fa-flag' id="myBtn" title="Zgłoś zawartość"></i></h2>
    <div class=" r-container">
      <div class="r-left">
    <?php
    $addOK=true;
    //czy wydarzenie jest aktywne
      $today = date("Y-m-d");
      if($today<$event[4])
      {
        echo "To wydarzenie rozpocznie się ".$event[4].".<br><br>";
        $addOK=false;
      }
      elseif($today>$event[5])
      {
        echo "To wydarzenie już się zakończyło.<br><br>";
        $addOK=false;
      }
    ?>

<!-- wyswietlanie -->

    <?php
    if($event[9]!=null)
    {
      echo "<a href='./profile.php?p=".$event[6]."'><img class=\"i-winner\" src='./uploads/avatars/".$event[9]."'></a>";
    }
    else
    {
      echo "<a href='./profile.php?p=".$event[6]."'><img class=\"i-winner\" src='./uploads/avatars/default.png'></a>";
    }
    ?>

    <span class="e-view e-nick"><?php echo "<a href='./profile.php?p=".$event[6]."'>".$event[6]."</a>"; ?></span><br>

      <label class="e-label">Początek wydarzenia: </label>
        <span class="e-view"><?php echo $event[4]; ?></span><br>

      <label class="e-label">Koniec wydarzenia: </label>
        <span class="e-view r"><?php echo $event[5]; ?></span>

      <?php if($event[3]!=""){ ?><span class="e-view"><?php echo "<a href='".urldecode($event[3])."'>Zalecana tabulatura</a><br>"; } ?></span><br>
      <?php $participants = $connect->query("SELECT COUNT(id) FROM fz_videos WHERE chall_id='$event[0]'");
      $participants = $participants->fetch_row();
      ?> <label class="e-label">Liczba zgłoszeń: </label><span class="e-view"><?php echo $participants[0] ?></span><br><br>

        <?php
        if(date("Y-m-d")<$event[4])
        {
          $ev_status="upcoming";
        }
        elseif(date("Y-m-d")>=$event[4] && date("Y-m-d")<$event[5])
        {
          $ev_status="active";
        }
        else
        {
          $ev_status="ended";
        }
        ?>

        <!-- tagi -->
        <?php
        foreach($tags as $tag)
        { ?>
          <label class="tag"><a href="./events-list.php?search_by_tag=<?php echo $tag ?>&status=<?php echo $ev_status; ?>"><?php echo $tag; ?></a></label><br> <?php
        } ?>

        <?php
        //możliwość nadesłania zgłoszenia, jeśli wydarzenie jest aktywne i użytkownik zalogowany
        if($addOK==true && isset($_SESSION["userid"]))
        { ?>
        <form action="./upload-video.php" method="post" id="form1">
        <br><button class="e-submit button-upload-file" type="submit" form="form1" name="add_to_event" value="<?php echo $event[0] ?>">Dodaj zgłoszenie</button>
        </form>
      <?php } elseif($addOK==true && !isset($_SESSION["userid"])) {?> <span> <?php echo "Musisz się zalogować, aby dodać zgłoszenie."; } ?></span>

        <!-- zobacz zgłoszenia -->
        <br><a href="./videos-list.php?search_by_event=<?php echo $result[0]; ?>"><button class="e-submit buttonShowZgloszenia">Zobacz zgłoszenia</button></a>

        <!-- usuwanie wydarzenia, jeśli zalogowany użytkownik jest właścicielem lub moderatorem -->
        <?php
          if(isset($_SESSION["userid"]))
          {
            $temp_var=$_SESSION["userid"];
            $user_rank = $connect->query("SELECT user_rank FROM fz_users WHERE id='$temp_var'");
            $user_rank = $user_rank->fetch_row();

            if($_SESSION["userid"]==$event[6] || $user_rank[0]>=3)
            {
              if($ev_status!="ended")
                { ?>
                <input type="submit" class="e-submit submit-edit" value="Edytuj wydarzenie" onclick="edit_event()" /> <?php
                }
                ?>
               <input type="submit" class="e-submit submit-delete" value="Usuń wydarzenie" onclick="remove_event()" />
               <?php
            }
          }
        ?>
  </div>

  <div class="r-right">
      <span class="e-view"><?php echo $event[2]; ?></span><br><br>
  </div>


    <script>
      function edit_event()
      {
        window.location='./edit-event.php';
      }
      function remove_event()
      {
        window.location='./remove-event.php';
      }
    </script>
    <!-- menu mobilne -->
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

    </nav>
    </div>
  </div>

  <script type="text/javascript">

    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];
    btn.onclick = function()
    {
      modal.style.display = "block";
    }

    span.onclick = function()
    {
      modal.style.display = "none";
    }

    window.onclick = function(event)
    {
      if (event.target == modal)
      {
        modal.style.display = "none";
      }
    }
  </script>

  <footer>
   <p>Copyright &copy 2020-2021 FretZone <i class='fas fa-envelope-open' style=" margin-left: 20px;"></i> <a href="mailto: welcome@fretzone.pl">welcome@fretzone.pl</a>
  </footer>
  </body>
</html>
