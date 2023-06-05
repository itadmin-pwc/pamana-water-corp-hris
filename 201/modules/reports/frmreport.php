<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form id="frmreport" name="frmreport" method="get" action="<?=$_GET['url']?>">
    <input type="hidden" value="<?=$_GET['empNo']?>" name="empNo" id="empNo" />
    <input type="hidden" value="<?=$_GET['empName']?>" name="empName" id="empName" />  
    <input type="hidden" value="<?=$_GET['empDiv']?>" name="empDiv" id="empDiv" />  
    <input type="hidden" value="<?=$_GET['empDept']?>" name="empDept" id="empDept" />  
    <input type="hidden" value="<?=$_GET['empSect']?>" name="empSect" id="empSect" />  
    <input type="hidden" value="<?=$_GET['groupType']?>" name="groupType" id="groupType" />  
    <input type="hidden" value="<?=$_GET['pafType']?>" name="pafType" id="pafType" />  
    <input type="hidden" value="<?=$_GET['code']?>" name="code" id="code" />  
    <input type="hidden" value="<?=$_GET['from']?>" name="from" id="from" />  
    <input type="hidden" value="<?=$_GET['to']?>" name="to" id="to" />  
    <input type="hidden" value="<?=$_GET['type']?>" name="type" id="type" />  
</form>
</body>
</html>
<script language="javascript">
	location.href='<?=$_GET['url'].'?'.$_GET['QUERY_STRING']."&type=".$_GET['type']."&code=".$_GET["code"]."&empNo=".$_GET["empNo"]."&empName=".$_GET["empName"]."&empDiv=".$_GET["empDiv"]."&empDept=".$_GET["empDept"]."&empSect=".$_GET["empSect"]."&groupType=".$_GET["groupType"]."&pafType=".$_GET["pafType"]."&from=".$_GET["from"]."&to=".$_GET["to"];?>';
</script>