<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Employee Tenure</title>
</head>

<body><input name="button" type="submit" class="style1" id="button" value="Back" onClick="location.href='../reports/headcount.php'">
<br>
<br>
<!-- saved from url=(0013)about:internet -->
<!-- amcolumn script-->
<script type="text/javascript" src="../../../includes/chart/amcolumn/swfobject.js"></script>
	<div id="flashcontent">
		<strong>You need to upgrade your Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[		
		var so = new SWFObject("../../../includes/chart/amcolumn/amcolumn.swf", "amcolumn", "520", "380", "8", "#FFFFFF");
		so.addVariable("path", "../../../includes/chart/amcolumn/");
		so.addVariable("settings_file", encodeURIComponent("headCount_settings.xml"));
		so.addVariable("data_file", encodeURIComponent("headCount_xml.php?&empDiv=<?=$_GET['empDiv']?>&empDept=<?=$_GET['empDept']?>"));
		so.addVariable("preloader_color", "#999999");
		so.write("flashcontent");
		// ]]>
	</script>
<!-- end of amcolumn script -->
</body>
</html>
