<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
    Select image to upload: <br><br>
    1. <input type="file" name="Filedata" id="Filedata"><br><br>
	<input type="hidden" name="huhu" id="huhu" value="grimace">
    <input type="submit" value="Convert ALL" name="submit"><br><br>
</form>

</body>
</html>

<?php
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

for($ayam=0;$ayam<count($cuba);$ayam++){
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
			failed();	
		}
	} 
	else {
		failed();
	}
}
function addTorrent($magnet, $namatorrent)
{
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