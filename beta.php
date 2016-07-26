<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
    Select zip to upload: (LONGGOKKAN FILE .torrent DALAM ZIP FILE DAN UPLOAD FILE ZIP TU)<br><br>
    1. <input type="file" name="Filedata" id="Filedata"><br><br>
	<input type="hidden" name="huhu" id="huhu" value="grimace">
    <input type="submit" value="Convert ALL" name="submit"><br><br>
</form>

<br>
<br>
<form action="" method="post">
Masukkan URL/Magnet Link: <input type="text" name="magneturl">
<input type="submit">
</form>
</body>
</html>

<?php
if (isset($_POST['magneturl'])){
	if (substr($_POST['magneturl'],0,6)=='magnet'){
		addTorrent($_POST['magneturl'], "a/Magnet Link");
		echo "<script>setTimeout('top.location = " . "\'" . 'betaing.php' .  "\'', 1000);" . "</script>";
	}
	else {
		if ($_POST['magneturl']==''){
			exit;
		}
		$a = file_get_contents($_POST['magneturl']);
		require_once('lightbenc.php');
		$info = Lightbenc::bdecode_getinfo($_POST['magneturl']);
			if (isset($info['info_hash'])) {
				//success($info['info_hash']);
				$magnetlink = 'magnet:?xt=urn:btih:'. strtoupper($info['info_hash']);
				addTorrent($magnetlink, "a/Magnet Link");
				//echo $magnetlink . '<br>';
				echo "<script>setTimeout('top.location = " . "\'" . 'betaing.php' .  "\'', 1000);" . "</script>";
				
			}
			else {
				echo "gagal magnet";
			}
	}
	exit;
}
$count = 0;
$ifok = false;
if (!isset($_POST['huhu'])){
	exit;
}
$zip = new ZipArchive;
if ($zip->open($_FILES['Filedata']['tmp_name']) === TRUE) {
    $zip->extractTo('tmp');
    $zip->close();
    $ifok = true;
}

if (!$ifok){
	exit;
}
$cuba = glob('tmp/*');
//var_dump($cuba);

for($ayam=0;$ayam<=count($cuba);$ayam++){
	if ($ayam==count($cuba)){
		echo "<script>setTimeout('top.location = " . "\'" . 'betaing.php' .  "\'', 1000);" . "</script>";
		exit;
	}
	$tempFile = file_get_contents($cuba[$ayam]);
	
	// Validate the file type
	$fileTypes = array('torrent');
	$fileParts = pathinfo($cuba[$ayam]);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		require_once('lightbenc.php');
		$info = Lightbenc::bdecode_getinfo($cuba[$ayam]);
		if (isset($info['info_hash'])) {
			//success($info['info_hash']);
			$magnetlink = 'magnet:?xt=urn:btih:'. strtoupper($info['info_hash']);
			//echo $magnetlink . '<br>';
			if(is_file($cuba[$ayam]) and ($cuba[$ayam] != __FILE__)) {
				addTorrent($magnetlink, $cuba[$ayam]);
				unlink($cuba[$ayam]);
			}
		}
		else {
			echo "gagal upload";
		}
	} 
	else {
		echo "gagal upload";
	}
}
function addTorrent($magnet, $namatorrent)
{
	for ($hh=0;$hh<2;$hh++){
	$cookie = "xxx"; // alldebrid uid cookie
	$tntmain = $magnet;
	$tnt = urldecode($tntmain);
	$decoded = urldecode($magnet);
	$headers = "Content-Type: application/x-www-form-urlencoded; charset=utf-8";
	$ch = curl_init();
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_REFERER, 'http://www.alldebrid.com/torrent/');
	curl_setopt($ch, CURLOPT_URL, 'http://upload.alldebrid.com/uploadtorrent.php');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	"Cookie: uid=" . $cookie
	));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'uid='.$cookie.'&domain=http%3A%2F%2Fwww.alldebrid.com%2Ftorrent%2F&magnet=' . urlencode($magnet) . '&quick=0&value=Convert%20this%20torrent');
	$returndata = curl_exec($ch);
	curl_close($ch);
	$string = $namatorrent;
	$namaarray = (explode('/',$string));
	$report = $namaarray[1]. ' - Torrent Added Successfully.'.'<br>';
	echo $report;
	}
}
function success($info_hash)
{
	$result = array('result'=>1,'url'=>'magnet:?xt=urn:btih:'.strtoupper($info_hash));
	$json = json_encode($result);
	if ($json)
	{
		//echo $json.'<br>';
		//echo 'magnet:?xt=urn:btih:'.strtoupper($info_hash).'<br>';
	}
}

function failed()
{
	$result = array('result'=>0,'url'=>null);
	$json = json_encode($result);
	if ($json)
	{
		echo $json;
	}
}
?>