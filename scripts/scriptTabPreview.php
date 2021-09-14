<?php
if(isset($_REQUEST["requested"]))
{
  $requestedText = $_REQUEST["requested"];

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

  $requestedText = htmlspecialchars($requestedText);
  $requestedText = preg_replace($bbcode,$htmlcode,$requestedText);
  $requestedText = str_replace("<?php","",$requestedText);
  $requestedText = str_replace("<?","",$requestedText);
  $requestedText = str_replace("?>","",$requestedText);
  $requestedText = str_replace("<script>","",$requestedText);
  $requestedText = str_replace("</script>","",$requestedText);
  echo $requestedText;
}
?>
