<?php session_start(); require_once('./scripts/scriptConnect.php'); ?>
<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <?php
    if(isset($_GET["v"]) && $_GET["v"]!="")
    {
      $tab_id=$_GET["v"];
    }
    else
    {
      ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
      die();
    }

    if(isset($_SESSION["tab_change"]))
    {
      unset($_SESSION["tab_change"]);
    }

    $result = $connect->query("SELECT fz_tabs.id, fz_tabs.name, fz_tabs.description, fz_tabs.tab, fz_tabs.added_date, fz_tabs.status, fz_users.login, fz_tabs.user_id, fz_users.avatar_url
                               FROM fz_tabs
                               JOIN fz_users
                               ON fz_tabs.user_id=fz_users.id
                               WHERE fz_tabs.id='$tab_id'");

    if($result!=null)
    {
      $result = $result->fetch_row();

      if($result[0]=="" || $result[5]!="OK")
      {
        ?> <script type="text/javascript">location.href = './not-found.php';</script> <?php
        die();
      }

      //przypisywanie danych rekordu do innej tablicy
      for($i=0;$i<count($result);$i++)
      {
        if($result[$i]!=null)
        {
          $tab[$i]=$result[$i];
        }
        else
        {
          $tab[$i]="";
        }
      }

      //zbierz z bazy id tagów
      $temp_tags_id = $connect->query("SELECT tag_id FROM fz_tabs_tags WHERE tab_id='$tab_id'");

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
      $tab[2] = htmlspecialchars($tab[2]);
      $temp_desc = $tab[2];
    	$tab[2] = preg_replace($bbcode,$htmlcode,$tab[2]);
      $tab[2] = str_replace("<?php","",$tab[2]);
      $tab[2] = str_replace("<?","",$tab[2]);
      $tab[2] = str_replace("?>","",$tab[2]);
      $tab[2] = str_replace("<script>","",$tab[2]);
      $tab[2] = str_replace("</script>","",$tab[2]);

      $tab[3] = htmlspecialchars($tab[3]);
      $temp_tab = $tab[3];
    	$tab[3] = preg_replace($bbcode,$htmlcode,$tab[3]);
      $tab[3] = str_replace("<?php","",$tab[3]);
      $tab[3] = str_replace("<?","",$tab[3]);
      $tab[3] = str_replace("?>","",$tab[3]);
      $tab[3] = str_replace("<script>","",$tab[3]);
      $tab[3] = str_replace("</script>","",$tab[3]);

      $_SESSION["tab_id"]=$tab[0];
      $_SESSION["tab_name"]=$tab[1];
      $_SESSION["tab_desc"]=$temp_desc;
      $_SESSION["tab_tab"]=$temp_tab;
    ?>

<!-- wyświetlanie zawartości strony -->
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/style2.css">
    <!-- rwd -->
    <link rel="stylesheet" href="./css/style_media-query-phone.css">
    <link rel="stylesheet" href="./font-awesome/css/all.css">
    <title><?php echo $tab[1]; ?> - FretZone</title>
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
          <p class="logo-text"><?php echo $tab[1]; ?></p>
        <?php include('./scripts/scriptBannerButtons.php'); ?>
      </div>

    <!-- container -->
<div class=" ul-container">

      <!-- tytul -->
    <span class="e-view"><?php echo $tab[1]; ?></span> <i class='fas fa-flag' id="myBtn" title="Zgłoś zawartość"></i><br>

    <?php
    if($tab[8]!=null)
    {
      echo "<a href='./profile.php?p=".$tab[6]."'><img class=\"i-winner\" src='./uploads/avatars/".$tab[8]."'></a>";
    }
    else
    {
      echo "<a href='./profile.php?p=".$tab[6]."'><img class=\"i-winner\" src='./uploads/avatars/default.png'></a>";
    }
    ?>

    <span class="e-view e-nick"><a href="./profile.php?p=<?php echo $tab[6]; ?>"><?php echo $tab[6]; ?></a></span><br>

  <label class="e-label">Dodano dnia:</label>
    <span class="e-view"><?php echo $tab[4]; ?></span></br></br>
<!-- opis -->
    <span class="e-view"><?php echo $tab[2]; ?></span></br></br>

    <!-- tagi -->
    <?php
    foreach($tags as $tag)
    { ?>
      <label class="tag"><a href="./tabs-list.php?search_by_tag=<?php echo $tag ?>"><?php echo $tag; ?></a></label> <?php
    } ?>
  </br></br>
    <!-- usuwanie tabulatury, jeśli zalogowany użytkownik jest właścicielem lub moderatorem -->
    <?php
      if(isset($_SESSION["userid"]))
      {
        $temp_var=$_SESSION["userid"];
        $user_rank = $connect->query("SELECT user_rank FROM fz_users WHERE id='$temp_var'");
        $user_rank = $user_rank->fetch_row();

        if($_SESSION["userid"]==$tab[7] || $user_rank[0]>=3)
        {
        ?> <input type="submit" class="e-submit submit-edit" value="Edytuj tabulaturę" onclick="edit_tab()" />
        <input type="submit" class="e-submit submit-delete" value="Usuń tabulaturę" onclick="remove_tab()" /> <?php
        }
      }
    ?>

    <script>
      function edit_tab()
      {
        window.location='./edit-tab.php';
      }
      function remove_tab()
      {
        window.location='./remove-tab.php';
      }
    </script>
  </br></br><hr></br>

<!-- sama tabulatura -->
    <span class="e-view" id="tab-content"><?php echo $tab[3]; ?></span><br><br>

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
      <!-- <a href="./moderator.php"><i class='icon fas fa-tachometer-alt'></i></a> -->
    </nav>
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
