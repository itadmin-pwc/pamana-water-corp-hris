<?
session_start();
include("../../../includes/CreateZipFile.inc.php"); 
if ($_GET['act'] == "") {
	$directoryToZip="./textfiles"; // This will zip all the file(s) in this present working directory
	$zipName="MBTC.zip";
} else {
	$directoryToZip="./AUB"; // This will zip all the file(s) in this present working directory
	$zipName="AUB.zip";
}
$file_txt = $_GET['file'];

$outputDir="/"; //Replace "/" with the name of the desired output directory.

$createZipFile=new CreateZipFile;


//Code toZip a directory and all its files/subdirectories
$createZipFile->zipDirectory($directoryToZip,$outputDir);

$fd=fopen($zipName, "wb");
$out=fwrite($fd,$createZipFile->getZippedfile());
fclose($fd);
$createZipFile->forceDownload($zipName);
@unlink($zipName);@unlink($file_txt);

?>