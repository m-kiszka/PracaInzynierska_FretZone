<?php
//użytkownik zalogowany minimum jako moderator
if(isset($_SESSION["userid"]))
{
  $id = $_SESSION["userid"];
  $rank_result = $connect->query("SELECT user_rank FROM fz_users WHERE id='$id'");
  unset($id);
  $rank_result = $rank_result->fetch_row();
  if(isset($rank_result) && $rank_result==-1)
  {
    ?> <script type="text/javascript">location.href = './scripts/scriptLogout.php';</script> <?php
    die();
  }
  if(!isset($rank_result) || $rank_result[0]=="" || $rank_result[0]<3)
  {
    ?> <script type="text/javascript">location.href = './index.php';</script> <?php
    die();
  }
}
else
{
  ?> <script type="text/javascript">location.href = './index.php';</script> <?php
  die();
}
?>
