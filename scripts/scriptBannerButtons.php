<?php
if(isset($_SESSION["userid"]))
{ ?>
  <a href="./scripts/scriptLogout.php" title="Wyloguj się"><i class='icon icon-logout fas fa-power-off'></i></a>
  <a href="./edit-account.php" title="Edytuj konto"><i class='icon fas fa-cog'></i></a>
  <a href="./profile.php" title="Pokaż profil"><i class='icon fas fa-user-alt'></i></a>
  <a href="./create.php" title="Dodaj zawartość"><i class='icon fas fa-plus'></i></a> <?php
}
else
{ ?>
  <a href="login.php"><button class="not-logged-button">Zaloguj</button></a>
  <a href="register.php"><button class="not-logged-button">Zarejestruj</button></a>
<?php } ?>
