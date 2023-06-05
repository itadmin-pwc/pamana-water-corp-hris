<?
session_start();
include("../../../includes/CreateZipFile.inc.php"); 
include("../../../includes/config.php");
if ($_GET['act'] == "") {
	$directoryToZip="./governmentals_textfiles/".$_GET["ZipfolderName"]; // This will zip all the file(s) in this present working directory
	$zipName=$_GET["ZipfolderName"].".zip";
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
@unlink($zipName);
unlink($_SERVER['DOCUMENT_ROOT']. SYS_NAME.'/payroll/modules/processing/governmentals_textfiles/'.$_GET["ZipfolderName"].'/'.$file_txt);



?>