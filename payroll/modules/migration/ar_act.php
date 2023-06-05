<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
$common = new commonObj();


switch($_GET['act']) {
	case "Edit":
		$sql = "Select ar.empNo,refNo,amount,empFirstName,empMidName,empLastName,lonTypeShortDesc 
				from tblARTransData ar 
					left join tblEmpMast emp on ar.empNo=emp.empNo 
					left join tblLoantype lon on ar.transType = lon.lontypeCd
				where ar.id='{$_GET['id']}'";
		$res = $common->getSqlAssoc($common->execQry($sql));
		$fullname = (trim($res['empNo']) != "") ? str_replace("Ñ","&Ntilde;",htmlentities($res['empLastName']). ", " . htmlentities($res['empFirstName']) ." ". htmlentities($res['empMidName'][0])) . ".":"";
	break;
	case "getEmpName":
		$sql = "Select empNo, empFirstName,empMidName,empLastName from tblEmpMast where empNo='{$_GET['empNo']}' and empStat IN ('RG','CN','PR')";
		$res = $common->getSqlAssoc($common->execQry($sql));
		$fullname = (trim($res['empNo']) != "") ? str_replace("Ñ","&Ntilde;",htmlentities($res['empLastName']). ", " . htmlentities($res['empFirstName']) ." ". htmlentities($res['empMidName'][0])) . ".":"";
		echo "$('fullname').innerHTML = '$fullname';\n";
		echo "$('empCheck').value = '{$res['empNo']}';\n";
		exit();
	break;
	case "Update":
		$Trns = $common->beginTran();
		$sql = "Update tblARTransData set empNo='{$_GET['txtempNo']}' where id = '{$_GET['id']}' \n";
		$Trns = $common->execQry($sql);
		$sqlCheck = "Select empno from tblCustomerNo where empNo='{$_GET['txtempNo']}'";
		$rsCheck = $common->execQry($sqlCheck);
		if ($common->getRecCount($rsCheck)>0) {
			$sqlCustNo = "Update tblCustomerNo set tblCustomerNo.custNo=ar.custNo from tblCustomerNo inner join tblARTransData ar on tblCustomerNo.empNo=ar.empNo where ar.id = '{$_GET['id']}'";	
		} else  {
			$sqlCustNo = "Insert into tblCustomerNo (compCode,custNo,empNo) Select '{$_SESSION['company_code']}',custNo,'{$_GET['txtempNo']}' from tblARTransData where id = '{$_GET['id']}' ";
		}
		if($Trns){
			$Trns = $common->execQry($sqlCustNo);
		}

		if(!$Trns){
			$Trns = $common->rollbackTran();
			echo "alert('AR Update failed.');\n";
		}
		else{
			$Trns = $common->commitTran();
			echo "alert('AR Update successful.');\n";	
		}			
		exit();
	break;
	case "LoadtoPayroll":
		$arrID = substr(trim($_POST['loadID']),1,strlen(trim($_POST['loadID']))-1);
		$sqlTag = "Update tblARTransData set status='T' where id in ($arrID)";
		$Trns = $common->beginTran();
		$Trns = $common->execQry($sqlTag);
		$sqlLoadToPayroll = "Insert into tblEmpLoans (
					compCode,
					empNo,
					lonTypeCd,
					lonRefNo,
					lonAmt,
					lonWidInterst,
					lonGranted,
					lonStart,
					lonSked,
					lonDedAmt1,
					lonDedAmt2,
					lonNoPaymnts,
					lonPaymentNo,
					lonCurbal,
					lonStat,
					UploadTag,
					mmsNo,
					dateadded,
					compGLCode,
					strName ) 
					SELECT    
						{$_SESSION['company_code']}, 
						empno, 
						transType, 
						refNo, 
						amount, 
						amount, 
						transDate, 
						transDate, 
						3, 
						dedAmt, 
						dedAmt, 
						NoDed, 
						0, 
						amount, 
						'O', 
						case ltrim(rtrim(strname)) when '' then 1 else 2 end as uploadTag, 
						invoiceNo, 
						'".date('Y-m-d')."',
						compGLCode,
						strName
					FROM tblARTransData where id IN ($arrID);
					";
		if ($Trns) {
			$Trns = $common->execQry($sqlLoadToPayroll);
		}
		if(!$Trns){
			$Trns = $common->rollbackTran();
			echo "alert('AR Loading failed.');\n";
		}
		else{
			$Trns = $common->commitTran();
			echo "alert('AR Loading successful.');\n";
			echo "window.open('loans_daily_pdf.php');\n";
			echo "window.open('ar_unloadedlist2.php');\n";
			echo "location.href='ar_list.php';\n";	
		}		
		exit();
	break;
}
?>

<HTML>
<head>
<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
<STYLE>@import url('../../style/payroll.css');</STYLE>
<style type="text/css">
<!--
	.headertxt {font-family: verdana; font-size: 11px;}
.style2 {font-family: verdana}
.style3 {font-size: 11px}
#frmAR .childGrid tr td #fullname {
	font-size: 11px;
}
#frmAR .childGrid tr td #fullname {
	font-family: Verdana;
}
-->
</style>

</head>
	<BODY>
	<form action="" method="post" name="frmAR" id="frmAR">
      <table width="414" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Employee No.</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=trim($res['empNo'])?>" onKeyPress="return getEmpName(event);" type="text" name="txtempNo" id="txtempNo" class="inputs" size="30">
          <input type="hidden" name="id" value="<?=$_GET['id']?>" id="id">
          <input type="hidden" name="empCheck" value="<?=trim($res['empNo'])?>" id="empCheck"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Name</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><div id="fullname"><?=$fullname?></div></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Loan Type</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><span class="gridDtlVal style2 style3">
            <?=$res['lonTypeShortDesc']?>
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Ref. No</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><span class="gridDtlVal">
            <input value="<?=$res['refNo']?>" type="text" name="txtrefNo" id="txtrefNo" class="inputs" size="30">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Amount</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><span class="gridDtlVal">
            <input value="<?=$res['amount']?>" type="text" name="txtamt" id="txtamt" class="inputs" size="30">
          </span></td>
        </tr>
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
            <input type="button" class="inputs" onClick="UpdateAr();" name="save" id="save" value="Update"></td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function UpdateAr() {
		var empInputs = $('frmAR').serialize(true);
		if (empInputs['empCheck'] == "") {
			alert('Employee Name is Required.');
			$('empCheck').focus();
            return false;		
		} 
		if (empInputs['txtrefNo'] == 0) {
			alert('Ref. No. is required.');
			$('txtrefNo').focus();
            return false;		
		}
		if (empInputs['txtamt'] == 0) {
			alert('Amount is required.');
			$('txtamt').focus();
            return false;		
		}		 		       
		params = 'ar_act.php?act=Update';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmAR').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
	function getEmpName(event) {
		if (window.event)
			key = window.event.keyCode;
		else if (event)
			key = event.which;
		else
			return true

		$('empCheck').value = '';
		$('fullname').innerHTML = '';
		if (key == 13) {
			var empNo = $('txtempNo').value;
			params = 'ar_act.php?act=getEmpName&empNo='+empNo;
			new Ajax.Request(params,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					
				}	
			});	
		}
	}
</script>
