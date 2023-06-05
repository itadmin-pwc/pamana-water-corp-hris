<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("payregister.obj.php");

$payRegisterObj = new payRegisterObj($_SESSION,$_GET);
$sessionVars = $payRegisterObj->getSeesionVars();
$payRegisterObj->validateSessions('','MODULES');

if($_GET["reportType"]==1)
{
	$tblPaySum = "tblPayrollSummaryHist";
	$tblEarn = 'tblEarningsHist';
	$tblAllw = 'tblAllowanceBrkDwnHist';
}
else
{
	$tblPaySum = "tblPayrollSummary";
	$tblEarn = 'tblEarnings';
	$tblAllw = 'tblAllowanceBrkDwn';
}

$payPdSlctd = $payRegisterObj->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '{$_GET['payPd']}'");				

$divList = $payRegisterObj->getDivList($tblPaySum,$tblEarn,$payPdSlctd['pdYear'],$payPdSlctd['pdNumber']);
switch ($_GET['action']){
	case 'valOvrTymBrkDwnRpt':
		
			if($payRegisterObj->getOvertimeBrkDwnDetails('check',$tblPaySum,$tblEarn,$payPdSlctd['pdYear'],$payPdSlctd['pdNumber'],$divList,'','') > 0){
				echo 1;
			}
			else{
				echo 2;
			}
		exit();
	break;
	case 'valAllwBrkDwnRpt':
			if($payRegisterObj->getAllowanceBrkDwnDetails('check',$tblPaySum,$tblAllw,$payPdSlctd['pdYear'],$payPdSlctd['pdNumber'],$divList,'','') > 0){
				echo 1;
			}
			else{
				echo 2;
			}
		exit();
	break;
	case 'valNDBrkDwnRpt':
			if($payRegisterObj->getNightDiffBrkDwnDetails('check',$tblPaySum,$tblEarn,$payPdSlctd['pdYear'],$payPdSlctd['pdNumber'],$divList,'','') > 0){
				echo 1;
			}
			else{
				echo 2;
			}
		exit();
	break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
</head>
<body>

		<div id="payregDiv"></div>
		<div id="indicator1" align="center"></div>

</body>
</html>

<script>
	pager('payregister_list.php','payregDiv','load',0,0,'','',<?php echo "'&payPd=".$_GET['payPd']."&payPdSlctd=".$_GET['pdYear']."&pdNumber=".$_GET['pdNumber']."&cmbBank=".$_GET['cmbBank']."&empNo=".$_GET['empNo']."&txtEmpName=".$_GET['txtEmpName']."&nameType=".$_GET['nameType']."&cmbDiv=".$_GET['cmbDiv']."&cmbDept=".$_GET['cmbDept']."&cmbSect=".$_GET['cmbSect']."&payPd".$_GET['payPd']."&reportType=".$_GET['reportType']."'"; ?>,'../../../images/');  
	function printPayReg(url,addr)
	{
		var check=0;
		document.getElementById('chotherded').value
		if (addr=='other_earnings_detailed_pdf.php' && document.getElementById('chotherinc').value==0) {
			check=1;
		}
		else if(addr=='loans_detailed_pdf.php' && document.getElementById('chloans').value==0) {
			check=1;
		}
		else if(addr=='other_deductions_detailed_pdf.php' && document.getElementById('chotherded').value==0) {
			check=1;
		}
		if (check==0) {
			window.open(url+'?'+'addr='+addr+<?php echo "'&payPd=".$_GET['payPd']."&payPdSlctd=".$_GET['payPdSlctd']."&pdNumber=".$_GET['pdNumber']."&cmbBank=".$_GET['cmbBank']."&empNo=".$_GET['empNo']."&txtEmpName=".$_GET['txtEmpName']."&nameType=".$_GET['nameType']."&cmbDiv=".$_GET['cmbDiv']."&cmbDept=".$_GET['cmbDept']."&cmbSect=".$_GET['cmbSect']."&payPd".$_GET['payPd']."&reportType=".$_GET["reportType"]."'"; ?>);		
		}
		else {
			alert('No record found!');
		}	
	}	
	function viewBrkDwnRpt(act){
		param = '<?php echo "&payPd=".$_GET['payPd']."&payPdSlctd=".$_GET['payPdSlctd']."&pdNumber=".$_GET['pdNumber']."&cmbBank=".$_GET['cmbBank']."&empNo=".$_GET['empNo']."&txtEmpName=".$_GET['txtEmpName']."&nameType=".$_GET['nameType']."&cmbDiv=".$_GET['cmbDiv']."&cmbDept=".$_GET['cmbDept']."&cmbSect=".$_GET['cmbSect']."&payPd".$_GET['payPd']."&reportType=".$_GET["reportType"].""; ?>'; 
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act+param,{
			method : 'get',
			onComplete : function (req){
				
				intRes = parseInt(req.responseText);
				if(act == 'valOvrTymBrkDwnRpt'){
					if(intRes == 1){
						window.open('payregister_overtime_brkdwn_pdf.php?payPd=<?=$_GET['payPd']?>&payPdSlctd=<?=$_GET['payPdSlctd']?>&pdNumber=<?=$_GET['pdNumber']?>&cmbBank=<?=$_GET['cmbBank']?>&empNo=<?=$_GET['empNo']?>&txtEmpName=<?=$_GET['txtEmpName']?>&cmbDiv=<?=$_GET['cmbDiv']?>&cmbDept=<?=$_GET['cmbDept']?>&cmbSect=<?=$_GET['cmbSect']?>&reportType=<?=$_GET['reportType']?>');					
					}
					if(intRes == 2){
						alert('NO RECORD FOUND');
					}
				}
				if(act == 'valAllwBrkDwnRpt'){
					if(intRes == 1){
						window.open('payregister_allow_brkdwn_pdf.php?payPd=<?=$_GET['payPd']?>&payPdSlctd=<?=$_GET['payPdSlctd']?>&pdNumber=<?=$_GET['pdNumber']?>&cmbBank=<?=$_GET['cmbBank']?>&empNo=<?=$_GET['empNo']?>&txtEmpName=<?=$_GET['txtEmpName']?>&cmbDiv=<?=$_GET['cmbDiv']?>&cmbDept=<?=$_GET['cmbDept']?>&cmbSect=<?=$_GET['cmbSect']?>&reportType=<?=$_GET['reportType']?>');					
					}
					if(intRes == 2){
						alert('NO RECORD FOUND');
					}					
				}
				if(act == 'valNDBrkDwnRpt'){
					if(intRes == 1){
						window.open('payregister_night_dif_brkdwn_pdf.php?payPd=<?=$_GET['payPd']?>&payPdSlctd=<?=$_GET['payPdSlctd']?>&pdNumber=<?=$_GET['pdNumber']?>&cmbBank=<?=$_GET['cmbBank']?>&empNo=<?=$_GET['empNo']?>&txtEmpName=<?=$_GET['txtEmpName']?>&cmbDiv=<?=$_GET['cmbDiv']?>&cmbDept=<?=$_GET['cmbDept']?>&cmbSect=<?=$_GET['cmbSect']?>&reportType=<?=$_GET['reportType']?>');					
					}
					if(intRes == 2){
						alert('NO RECORD FOUND');
					}						
				}
			},
			onCreate : function (){
				$('indicator2').src="../../../images/wait.gif";
			},
			onSuccess : function (){
				$('indicator2').src='../../../images/refresh.gif';
			}
		})
	}
</script>
