<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("profile_paf_obj.php");
$maintEmpObj = new pafObj($_GET,$_SESSION);;
$empNo 		= $_GET['empNo'];
$compCode 	= $_GET['compCode'];
$refNo		= $_GET['refNo'];
$type 		= $_GET['type'];
$empProf =  $maintEmpObj->getEmployee($compCode,$empNo,'');
switch($_GET['type']) {
	case '1':
		$table='tblPAF_EmpStatus';
	break;	
	case '2':
		$table='tblPAF_Branch';
	break;	
	case '3':
		$table='tblPAF_Position';
	break;	
	case '4':
		$table='tblPAF_PayrollRelated';
	break;	
	case '5':
		$table='tblPAF_Others';
	break;	
	case '6':
		$table='tblPAF_Allowance';
	break;	
}

switch($_GET['code']) {
	case "GetRefNo":
		$arrRefNo = $maintEmpObj->makeArr($maintEmpObj->getPAFlist($empNo,$compCode,$table),'refNo','refNo','');
		$maintEmpObj->DropDownMenu($arrRefNo,'cmbrefno',"",'class="inputs" style="width:150px;"');				
		exit();
	break;
	case "Delete":	
		if ($maintEmpObj->delrefNo($empNo,$compCode,$table,$refNo)) {
			echo "GetRefNo($type,$empNo,$compCode);";
			echo "alert('PAF successfully deleted.');";
		} else {
			echo "alert('PAF deletion failed.');";
		}
		exit();
	break;
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<STYLE>@import url('../../style/tabs.css');</STYLE>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
        
		<style type="text/css">
			.headertxt {font-family: verdana; font-size: 11px;}
			.style5 {font-size: 11px}
        </style>        
	</HEAD>
	<BODY >
		<FORM name='frmActionType' id="frmActionType" action="" onSubmit="return validateTabs('<?=$_GET['act']?>');" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="370">
			  <tr>
					
      <td width="366" height="30" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp; 
        ACTION TYPE</td>
			  </tr>
                <tr>
                  <td class="parentGridDtl" ><table width="358" border="0" align="center" cellpadding="1" cellspacing="1" class="childGrid">
                    <tr>
                      <td height="25" colspan="3" class="gridToolbar"><table width="122" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                          <td width="105"><img style="cursor:pointer" onClick="PAF('Add','<?=$_GET['empNo']?>','<?=$_GET['compCode']?>');" src="../../../images/application_form_add.png" width="16" height="16">&nbsp;<img src="../../../images/application_form_edit.png" style="cursor:pointer" onClick="PAF('Edit','<?=$_GET['empNo']?>','<?=$_GET['compCode']?>');" width="16" height="16">&nbsp;<img src="../../../images/application_form_delete.png" width="16"  style="cursor:pointer" onClick="PAF('Delete','<?=$_GET['empNo']?>','<?=$_GET['compCode']?>');" height="16"></td>
                        </tr>
                      </table>
                     </td>
                    </tr>
                    <tr>
						<td height="20"><span class="gridDtlLbl2 style5" >Employee No.</span></td>
						<td><div align="center"><span class="style3">:</span></div></td>
						<td height="20"><span class="headertxt">
						<?=$empProf['empNo'];?></span></td>
					</tr>
					<tr>
						<td height="20"><span class="gridDtlLbl2 style5">Name</span></td>
						<td><div align="center"><span class="style3">:</span></div></td>
						<td height="20"><span class="headertxt"><?=$empProf['empLastName'] . ", " . $empProf['empFirstName'] . " " . $empProf['empMidName'];?></span></td>
					</tr>
                    <tr>
                      <td width="83" class="gridDtlLbl style5">PAF Type</td>
                      <td width="5" class="gridDtlLbl style5">:</td>
                      <td width="256" height="26"><font class="byOrder">
                        <?
                         	$userbranch = $maintEmpObj->getEmployee($_SESSION['company_code'],$_SESSION['employee_number'],"");
							//OLD
			   				// if ((($_SESSION['user_level'] == 1)||($_SESSION['user_level'] == 2)&&($userbranch['empBrnCode']=="999"))) 
							//NEW
							// if ($_SESSION['Confiaccess'] == "Y" && $_SESSION['user_level'] !== "1")
			   				// {
						    //     //$maintEmpObj->DropDownMenu(array('','1'=>'EMPLOYMENT STATUS','2'=>'BRANCH','3'=>'POSITION','4'=>'PAYROLL RELATED','5'=>'OTHERS','6'=>'ALLOWANCE'),'type',$orderBy,'class="inputs" onChange="GetRefNo(this.value,\''.$empNo.'\',\''.$compCode.'\')" '); 
						    //     $maintEmpObj->DropDownMenu(array('','4'=>'PAYROLL RELATED','6'=>'ALLOWANCE'),'type',$orderBy,'class="inputs" onChange="GetRefNo(this.value,\''.$empNo.'\',\''.$compCode.'\')" '); 
                         	// }
							// elseif($_SESSION['Confiaccess'] != "Y" && $_SESSION['user_level'] !== "1")
							// {
							// 	$maintEmpObj->DropDownMenu(array('','1'=>'EMPLOYMENT STATUS','2'=>'BRANCH','3'=>'POSITION','5'=>'BASIC INFORMATION'),'type',$orderBy,'class="inputs" onChange="GetRefNo(this.value,\''.$empNo.'\',\''.$compCode.'\')" '); 
							// }else{
							// 	$maintEmpObj->DropDownMenu(array('','1'=>'EMPLOYMENT STATUS','2'=>'BRANCH','3'=>'POSITION','4'=>'PAYROLL RELATED','5'=>'OTHERS','6'=>'ALLOWANCE'),'type',$orderBy,'class="inputs" onChange="GetRefNo(this.value,\''.$empNo.'\',\''.$compCode.'\')" '); 
							// }
							//show all
							$maintEmpObj->DropDownMenu(array('','1'=>'EMPLOYMENT STATUS','2'=>'BRANCH','3'=>'POSITION','4'=>'PAYROLL RELATED','5'=>'OTHERS','6'=>'ALLOWANCE'),'type',$orderBy,'class="inputs" onChange="GetRefNo(this.value,\''.$empNo.'\',\''.$compCode.'\')" '); 
						 ?>
                      </font></td>
                    </tr>
                    <tr>
                      <td class="gridDtlLbl2 style5">Ref. No</td>
                      <td class="gridDtlLbl2 style5">:</td>
                      <td height="26"><div align="left" id="divrefNo">
                      <select  style="width:150px;" class="inputs" name="cmbrefno" id="cmbrefno">
                        <option value=""></option>
                      </select>
                      </div></td>
                    </tr>
                    
                  </table></td>
              </tr>
			</TABLE>
	</FORM>
</BODY>
</HTML>
<SCRIPT>
	function goto(act,empNo,compCode) {
		var refNo = document.frmActionType.cmbrefno.value;
		location.href = "profile_transaction.php?act="+act+"&empNo="+empNo+"&compCode="+compCode;
	}
	
	function PAFEdit(str) {
		if (str !="") {
			location.href = "profile_transaction.php?"+str;
		}
	}
	
	function PAF(act,empNo,compCode) {
		var type = document.frmActionType.type.value;
		var refNo = document.frmActionType.cmbrefno.value;
		var url;
		if (type==0) {
			alert('Please select PAF Type');
			return false;
		}
		switch(type) {
			case '1':
				url='empstat';
			break;	
			case '2':
				url='branch';
			break;	
			case '3':
				url='position';
			break;	
			case '4':
				url='payroll';
			break;	
			case '5':
				url='others';
			break;	
			case '6':
				url='payroll';
			break;	
		}
		switch(act) {
			case 'Add':
				location.href = "profile_transaction.php?act="+url+"&empNo="+empNo+"&compCode="+compCode+"&trantype=A";
			break;
			case 'Edit':
				if (refNo=="") {
					alert('Please select Ref. No.');
					return false;
				}
				location.href = 'profile_transaction.php?act='+url+'&empNo='+empNo+'&compCode='+compCode+'&frmRefNo='+refNo+"&trantype=E";
			break;
			case 'Delete':
				if (refNo=="") {
					alert('Please select Ref. No.');
					return false;
				}
				var del = confirm('Are you sure want to delete this record?');
				if (del==true) {
					new Ajax.Request(
							  'profile_actionlist.php?empNo='+empNo+'&type='+type+'&compCode='+compCode+'&code=Delete&refNo='+refNo,
							  {
								 asynchronous : true,     
								 onComplete   : function (req){
									eval(req.responseText);
								 }
							  }
							);
				}							
			break;
		}
	}
	
	function GetRefNo(type,empNo,compCode) {
		new Ajax.Request(
			  'profile_actionlist.php?empNo='+empNo+'&type='+type+'&compCode='+compCode+'&code=GetRefNo',
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					$('divrefNo').innerHTML=req.responseText;
				 }
			  }
			);	
	}
</SCRIPT>
