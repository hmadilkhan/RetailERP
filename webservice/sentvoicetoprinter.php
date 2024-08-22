<?php
require "printtest.php";
date_default_timezone_set("Asia/Karachi");

$serial = $_POST['serial'];
$text = $_POST['text'];

if($serial != ""){
	echo sendVoiceToPrinter($serial,$text,'');
}
function sendVoiceToPrinter($sn,$content,$link)
{
	$printer = new SunmiCloudPrinter();
	// $sn = "N411231A00721";
	$printer->pushVoice($sn,$content,$link);
}
?>