 <br><br><center>
 
<?php

$eelink = str_replace(",;,", " , ", urldecode($_POST['link']));
$anchor_tag = urldecode($_POST['link']);
preg_match_all('/^<a.*?value=(["\'])(.*?)\1.*$/', $anchor_tag, $matches);

if ($matches && isset($matches[2][0]))
  {
  $anchor_href = $matches[2][0];
  $href_attr_data = explode(",;,", $anchor_href);
  foreach($href_attr_data as $href_key => $href_value)
    {
    if ($href_value)
      {
      $anchor_cnt = $href_key + 1;
      $href_txt = 'File ' . $anchor_cnt;
      echo '<font color="black"><a href="' . $href_value . '" class="uptolinks" id="uptolinks" alt="Uptobox" target="_blank"> ' . $href_value . '</a></font>';
	  //echo "<script>setTimeout('top.location = " . "\'" . $href_value .  "\'', 1000);" . "</script>";
      }
    }
  }
  else
  {
  echo "Anchor Href Values Not Found!!!";
  }

  

?>   

</center>   