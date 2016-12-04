<?php
if (isset($_POST['convert'])){
	require "uploader.php";
	$upload = new Upload();
	if ( ! $upload->do_upload("lrc")){ //check for errors
		die($upload->error());
	}
	$data = $upload->data();
	$input = $data['file_path'];
	$output = "srt/".trim($data['file_name'], '.lrc').'_'.time().".srt"; //set output name
	$get = file_get_contents($input); //read lrc file
	$whole = "";
	preg_match_all("/\[(\d{2}:\d{2}\.\d{2})\](.*)/", $get, $out);
	foreach($out[1] as $k => $i){
		$res = ($k+1).PHP_EOL; //numbering
		$end = "00:".str_ireplace(".",",",$i)."0"; //initiate the end of a line
		if (isset($out[1][$k+1])){ //check if next line is exists
			$end = "00:".str_ireplace(".",",",$out[1][$k+1])."0"; //assign this end time with next line's start time
		}
		$res .= "00:".str_ireplace(".",",",$i)."0 --> ".$end.PHP_EOL; //time part
		$res .= $out[2][$k].PHP_EOL.PHP_EOL; //text part, you can add font or something
		$whole .= $res; //append to the $whole
	}
	file_put_contents($output, $whole);//write it
	$zipname = trim($data['file_name'], '.lrc').'_'.$_SERVER['HTTP_HOST'].'.zip'; //zip name
	$zip = new ZipArchive;
	$zip->open($zipname, ZipArchive::CREATE);
	$zip->addFile($input);
	$zip->addFile($output);
	$zip->close();
	unlink($input);
	unlink($output);
	//download the zipped file.
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$zipname);
	header('Content-Length: ' . filesize($zipname));
	readfile($zipname);
}
?>
<html>
	<head>
		<title>LRC to SRT</title>
		<style>
			#form-input{
				text-align: center;
				border: 1px solid #DDD;
				padding-top: 25px;
				padding-bottom: 17px;
				margin-left: 20%;
				margin-right: 20%;
				border-radius: 8px;
				background-color: #ecebeb;
			}
		</style>
	</head>
	<body>
		
		<div id="form-input">
			<h1>Convert .LRC to .SRT</h1>
			<form method="POST" enctype="multipart/form-data">
				<input type="file" name="lrc" />
				<input type="submit" name="convert" value="CONVERT"/>
			</form>
		</div>
	</body>
</html>