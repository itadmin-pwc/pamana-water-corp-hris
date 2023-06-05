<?
include("profile.obj.php");
if ($_GET['code']=="delcontact"){
	$recNo=$_GET['recNo'];
	$profileobj = new ProfileObj();
	$res=$profileobj->employeeaction("Delete","","","",$recNo);
	if ($res) {
		echo "alert('Contact info Deleted!');";
	}
	else {
		echo "alert('Contact info Deletion failed!');";
	}
	
	exit();
}

if ($_GET['code']=="8"){
	$profileobj=new ProfileObj();
	$profileobj->employeeaction($_GET['act'],$_GET['contacttype'],$_GET['contactdesc'],$_GET['empNo'],$_GET['recNo']);
	exit();
}

?>