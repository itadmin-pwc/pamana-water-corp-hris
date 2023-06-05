<?
session_start();
	session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("chart_obj.php");

$chartObj = new chartObj();
$compCode = $_SESSION['company_code'];
$empNo = $_GET['empNo'];
$empInfo = $chartObj->getUserInfo($compCode,$empNo,'');
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
.style2 {
	font-family: Verdana;
	font-size: 12px;
}
-->
</style>
</head>
<body>
<input name="button" type="submit" class="style1" id="button" value="Back" onClick="location.href='../reports/inq_empAbsences.php'">
<br>
<br>
<div class="style2">&nbsp;&nbsp;&nbsp;<strong>Emp. Name:</strong>&nbsp;&nbsp;<?=$empInfo['empLastName'] .', '.$empInfo['empFirstName'] . ' ' . $empInfo['empMidName']?><br>
&nbsp;&nbsp;<strong>&nbsp;Emp. No.:&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp; 
<?=$empNo?><br>
</div>
<br>
<br>


<script type="text/javascript" src="../../../includes/chart/amline/swfobject.js"></script>
	<div id="flashcontent"  style="float:left;">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("../../../includes/chart/amline/amline.swf", "amline", "420", "300", "8", "#FFFFFF");
		so.addVariable("path", "../../../includes/chart/amline/");
		so.addVariable("settings_file", encodeURIComponent("absences_settings_xml.php?label=Absences"));
		so.addVariable("data_file", encodeURIComponent("absences_xml.php?empNo=<?=$_GET['empNo']?>&Year=<?=date('Y')?>&act=Absences"));
		so.write("flashcontent");
		// ]]>
	</script>
	<div id="flashcontent2" >
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("../../../includes/chart/amline/amline.swf", "amline", "420", "300", "8", "#FFFFFF");
		so.addVariable("path", "../../../includes/chart/amline/");
		so.addVariable("settings_file", encodeURIComponent("absences_settings_xml.php?label=Tardiness"));
		so.addVariable("data_file", encodeURIComponent("absences_xml.php?empNo=<?=$_GET['empNo']?>&Year=<?=date('Y')?>&act=Tardiness"));
		so.write("flashcontent2");
		// ]]>
	</script><br>

	<div id="flashcontent3" style="float:left;">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("../../../includes/chart/amline/amline.swf", "amline", "420", "300", "8", "#FFFFFF");
		so.addVariable("path", "../../../includes/chart/amline/");
		so.addVariable("settings_file", encodeURIComponent("absences_settings_xml.php?label=Undertime"));
		so.addVariable("data_file", encodeURIComponent("absences_xml.php?empNo=<?=$_GET['empNo']?>&Year=<?=date('Y')?>&act=Undertime"));
		so.write("flashcontent3");
		// ]]>
	</script>

</body>
</html>
