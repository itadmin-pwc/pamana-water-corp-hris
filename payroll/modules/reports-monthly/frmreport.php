<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form id="frmreport" name="frmreport" method="post" action="<?=$_GET['url']?>">
    <input type="hidden" value="<?=$_GET['empNo']?>" name="empNo" id="empNo" />
    <input type="hidden" value="<?=$_GET['empName']?>" name="empName" id="empName" />  
    <input type="hidden" value="<?=$_GET['empDiv']?>" name="empDiv" id="empDiv" />  
    <input type="hidden" value="<?=$_GET['empDept']?>" name="empDept" id="empDept" />  
    <input type="hidden" value="<?=$_GET['empSect']?>" name="empSect" id="empSect" />  
    <input type="hidden" value="<?=$_GET['groupType']?>" name="groupType" id="groupType" />  
    <input type="hidden" value="<?=$_GET['orderBy']?>" name="orderBy" id="orderBy" />  
    <input type="hidden" value="<?=$_GET['catType']?>" name="catType" id="catType" />  
    <input type="hidden" value="<?=$_GET['table']?>" name="table" id="table" />  
    <input type="hidden" value="<?=$_GET['payPd']?>" name="payPd" id="payPd" />
</form>
</body>
</html>
<script language="javascript">
	document.frmreport.submit();
</script>