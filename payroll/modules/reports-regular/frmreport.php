<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form id="frmreport" name="frmreport" method="post" action="<?=$_GET['addr']?>">
    <input type="hidden" value="<?=$_GET['empNo']?>" name="empNo" id="empNo" />
    <input type="hidden" value="<?=$_GET['txtEmpName']?>" name="txtEmpName" id="txtEmpName" />    <input type="hidden" value="<?=$_GET['cmbDiv']?>" name="cmbDiv" id="cmbDiv" />  
    <input type="hidden" value="<?=$_GET['empDept']?>" name="empDept" id="empDept" />  
    <input type="hidden" value="<?=$_GET['empSect']?>" name="empSect" id="empSect" />  
    <input type="hidden" value="<?=$_GET['catType']?>" name="catType" id="catType" />  
    <input type="hidden" value="<?=$_GET['table']?>" name="table" id="table" />  
    <input type="hidden" value="<?=$_GET['payPd']?>" name="payPd" id="payPd" />
    <input type="hidden" value="<?=$_GET['payPdSlctd']?>" name="payPdSlctd" id="payPdSlctd" />
    <input type="hidden" value="<?=$_GET['pdNumber']?>" name="pdNumber" id="pdNumber" />
    <input type="hidden" value="<?=$_GET['cmbBank']?>" name="cmbBank" id="cmbBank" />
    <input type="hidden" value="<?=$_GET['nameType']?>" name="nameType" id="nameType" />
    <input type="hidden" value="<?=$_GET['reportType']?>" name="reportType" id="reportType" />
</form>

</body>
</html>
<script language="javascript">
	document.frmreport.submit();
</script>