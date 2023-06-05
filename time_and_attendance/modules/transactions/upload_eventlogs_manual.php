<?
##################################################
session_start(); 
$domain = $_SERVER['REMOTE_ADDR'];

include("../../../includes/db.inc.php");
include("../../../includes/common.php");
$commonObj = new commonObj();
$sessionVars = $commonObj->getSeesionVars();
$commonObj->validateSessions('','MODULES');

switch($_GET['act']) {
	case 'proc' :
			$sqlGetPayDate = "SELECT brn.brnCode, brn.brnDefGrp, pprd.pdFrmDate, pprd.pdToDate 
							  FROM tblBranch brn
							  INNER JOIN  tblPayPeriod pprd ON brn.compCode = pprd.compCode 
							  	AND brn.brnDefGrp = pprd.payGrp
							  WHERE brn.brnCode='".$_GET['brnCode']."' AND pprd.pdStat='O' AND pprd.payCat='3'";
			$resPayPeriod = $commonObj->getSqlAssoc($commonObj->execQry($sqlGetPayDate));
			$frDate = date('Ymd',strtotime($resPayPeriod['pdFrmDate']));
			$toDate = date('Ymd',strtotime($resPayPeriod['pdToDate']));  	

			$sqlDelete = "Delete from tblTK_EventLogs where cStoreNum='{$_GET['brnCode']}' AND (EDATE>='". $frDate."' AND EDATE<='".$toDate."')";
			$commonObj->execQry($sqlDelete);
			
			$file = fopen("http://$domain/Data/brnch_".$_GET['brnCode'].".txt","r");//fopen("http://".$domain."/C:/brnch_".$_GET['brnCode'].".txt","r");
			$counter=0;
			$Trns = $commonObj->beginTran();
			$err = true;
			$sqlInsert = ""; 
			$sqlArrEmp = "SELECT tblTK_EmpShift.empNo, tblTK_EmpShift.bioNo, tblEmpMast.empBrnCode AS brnCode 
						  FROM tblTK_EmpShift 
						  INNER JOIN tblEmpMast ON tblTK_EmpShift.empNo COLLATE DATABASE_DEFAULT = tblEmpMast.empNo COLLATE DATABASE_DEFAULT 
						  	AND tblTK_EmpShift.compCode = tblEmpMast.compCode 
						  Where tblTK_EmpShift.compCode='{$_SESSION['company_code']}' And empBrnCode='{$_GET['brnCode']}'";
			$arrArrEmp = $commonObj->getArrRes($commonObj->execQry($sqlArrEmp));
			while(! feof($file))
			  {
				
				 $array_rec = str_replace('"',"",fgets($file));
				 $array_rec = explode(",",$array_rec);
				 $compCode = $_SESSION['company_code'];
					foreach($arrArrEmp as $valEmp) {
						if ($valEmp['bioNo']==$array_rec[6]) {
							$err = false;
							$datetime = date('m/d/Y H:i',strtotime($array_rec[1]." ".$array_rec[2]));
							$data['cStoreNum']	= $_GET['brnCode'];
							$data['EDATE']		= $array_rec[1];
							$data['ETIME'] 		= $array_rec[2];
							$data['EDOOR'] 		= $array_rec[3];
							$data['EFLOOR'] 	= $array_rec[4];
							$data['ESABUN'] 	= $array_rec[5];
							$data['ETAG'] 		= $array_rec[6];		
							$data['ENAME']		= $array_rec[7];
							$data['ELNAME']		= $array_rec[8];
							$data['EPART'] 		= $array_rec[9];
							$data['EDEP']		= $array_rec[10];
							$data['ESTATUS'] 	= $array_rec[11];
							$data['EFUNCTION'] 	= $array_rec[12];
							$data['EINOUT']		= $array_rec[13];
							$counter++;					
							$sqlInsert .= " Insert into tblTK_EventLogs (cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT) values 
							(
							'{$data['cStoreNum']}',
							'{$data['EDATE']}',
							'{$data['ETIME']}',
							'{$data['EDOOR']}',
							'{$data['EFLOOR']}',
							'{$data['ESABUN']}',
							'{$data['ETAG']}',	
							'{$data['ENAME']}',
							'{$data['ELNAME']}',
							'{$data['EPART']}',
							'{$data['EDEP']}',
							'{$data['ESTATUS']}',
							'{$data['EFUNCTION']}',
							'{$data['EINOUT']}'
							); 
							";
						}
					}
					
			  }	
		 fclose($file);
		if ($Trns && $sqlInsert !="") {
			$Trns = $commonObj->execQry($sqlInsert);
		}
		$sql = "SELECT TOP 1 MAX(ETIME) AS ETIME, EDATE FROM tblTK_EventLogs WHERE cStoreNum='{$_GET['brnCode']}' GROUP BY EDATE ORDER BY EDATE DESC";	
		$arrmaxTime = $commonObj->getSqlAssoc($commonObj->execQry($sql));
		$datetime = date('m/d/Y H:i',strtotime($arrmaxTime['EDATE']." ".$arrmaxTime['ETIME']));
		if (!$Trns || $sqlInsert =="") {
			$Trns = $commonObj->rollbackTran();
			echo "<script>alert('Error Uploading Event Logs!')</script>";
		} else {
			$Trns = $commonObj->commitTran();
			echo "<script>alert('Event Logs Successfully Uploaded, latest uploaded log(s): $datetime')</script>";
		}	
	break;
}

?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<STYLE>@import url('../../style/payroll.css');</STYLE>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../js/extjs/adapter/prototype/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">
@import url("../../../includes/calendar/calendar-blue.css");.style1 {font-size: 13px}
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style4 {font-size: 13px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
.style5 {font-size: 15px}
</STYLE>
<!--end calendar lib-->
</HEAD>
	<BODY>
<form name="frmGenerateloans" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="50%">
    <tr>
		
      <td width="408" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Upload Event Logs</td>
	</tr>
	<tr>
		<td height="122" class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          
          <tr>
            <td width="26%" height="25" class="style1 style2">Company</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2"><font class="gridDtlLblTxt" id="compCode">
              <?
										$compName = $commonObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
            </font></span></td>
          </tr>
          <tr> 
            <td height="25" class="style1 style2">Branch</td>
            <td width="2%" class="style1">:</td>
            <td colspan="4" width="72%" class="gridDtlVal style5">&nbsp;
			<?
            	$brn = $commonObj->makeArr($commonObj->getBranch($_SESSION['company_code']),'brnCode','brnDesc','');
                $commonObj->DropDownMenu($brn,'cmbBranch','','class="inputs"' );
			?>            </td>
          </tr>
          
		  <tr>
		    <td height="25" colspan="7" class="childGridFooter">
							<div align="center">
							  <input name="btnProcess" type="button" class="inputs" id="btnProcess" onClick="ProcessTS();" value="Upload Event Logs">
			               </div></td>
		    </tr>
        </table>
<div id="caption" align="center">        </div>				
	</td>
	</tr> 
</table>
</form>
</BODY>
</HTML>
<script type="text/javascript">
// JavaScript Document
	function ProcessTS() {
		if($('cmbBranch').value==0){
			alert('Branch is required! Please select branch...');
			$('cmbBranch').focus();	
			return false;
		}
		
		timedCount();
		$('btnProcess').disabled=true;
		var brnchcode = $('cmbBranch').value;
		location.href='<?=$_SERVER['PHP_SELF']?>?brnCode='+brnchcode+'&act=proc';
		
	}
	
	var m=0;
	var s=0;
	var t;	

	function timedCount(){

		if(s == 60){
			m = m+1;
		}	
		if(s == 60){
			s =0;
		}

		$('caption').innerHTML="<font size='2'>"+m+":"+s+ " <blink>Uploading Event Logs...</blink></font> " +'<br><img src="../../../images/progress2.gif">';
		s=s+1;
		t=setTimeout("timedCount()",1000);
	}
	
	function stopCount(){
		clearTimeout(t);
	}	
</script>
<?
//if ($_GET['act']=='Insert') {
//	//if (in_array($_GET['brnCode'],array(49))) {
//	if (in_array($_GET['brnCode'],array(500))) {	
//		$file = fopen("ftp://pgho:pg2007@192.168.200.108/HRIS/Data/br_{$_GET['brnCode']}.txt", "r");
//	} else {
//		$file = fopen("http://$domain/Data/sample.txt","r");
//	}
//		$counter=0;
//		$Trns = $commonObj->beginTran();
//		$err = true;
//		$sqlInsert = ""; 
//		$sqlArrEmp = "SELECT     tblTK_EmpShift.empNo, tblTK_EmpShift.bioNo, tblEmpMast.empBrnCode AS brnCode FROM tblTK_EmpShift INNER JOIN tblEmpMast ON tblTK_EmpShift.empNo COLLATE DATABASE_DEFAULT = tblEmpMast.empNo COLLATE DATABASE_DEFAULT AND tblTK_EmpShift.compCode = tblEmpMast.compCode Where tblTK_EmpShift.compCode='{$_SESSION['company_code']}' And empBrnCode='{$_GET['brnCode']}'";
//		$arrArrEmp = $commonObj->getArrRes($commonObj->execQry($sqlArrEmp));
//		while(! feof($file))
//		  {
//		  	
//			 $array_rec = str_replace('"',"",fgets($file));
//			 $array_rec = explode(",",$array_rec);
//			 $compCode = $_SESSION['company_code'];
//				foreach($arrArrEmp as $valEmp) {
//					if ($valEmp['bioNo']==$array_rec[6]) {
//						$err = false;
//						$datetime = date('m/d/Y H:i',strtotime($array_rec[1]." ".$array_rec[2]));
//						$data['cStoreNum']	= $_GET['brnCode'];
//						$data['EDATE']		= $array_rec[1];
//						$data['ETIME'] 		= $array_rec[2];
//						$data['EDOOR'] 		= $array_rec[3];
//						$data['EFLOOR'] 	= $array_rec[4];
//						$data['ESABUN'] 	= $array_rec[5];
//						$data['ETAG'] 		= $array_rec[6];		
//						$data['ENAME']		= $array_rec[7];
//						$data['ELNAME']		= $array_rec[8];
//						$data['EPART'] 		= $array_rec[9];
//						$data['EDEP']		= $array_rec[10];
//						$data['ESTATUS'] 	= $array_rec[11];
//						$data['EFUNCTION'] 	= $array_rec[12];
//						$data['EINOUT']		= $array_rec[13];
//						$counter++;					
//						$sqlInsert .= " Insert into tblTK_EventLogs (cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT) values 
//						(
//						'{$data['cStoreNum']}',
//						'{$data['EDATE']}',
//						'{$data['ETIME']}',
//						'{$data['EDOOR']}',
//						'{$data['EFLOOR']}',
//						'{$data['ESABUN']}',
//						'{$data['ETAG']}',	
//						'{$data['ENAME']}',
//						'{$data['ELNAME']}',
//						'{$data['EPART']}',
//						'{$data['EDEP']}',
//						'{$data['ESTATUS']}',
//						'{$data['EFUNCTION']}',
//						'{$data['EINOUT']}'
//						); 
//						";
//					}
//				}
//				
//		  }	
//	 fclose($file);
//	if ($Trns && $sqlInsert !="") {
//		$Trns = $commonObj->execQry($sqlInsert);
//	}
//	$sql = "SELECT TOP 1 MAX(ETIME) AS ETIME, EDATE FROM tblTK_EventLogs WHERE cStoreNum='{$_GET['brnCode']}' GROUP BY EDATE ORDER BY EDATE DESC";	
//	$arrmaxTime = $commonObj->getSqlAssoc($commonObj->execQry($sql));
//	$datetime = date('m/d/Y H:i',strtotime($arrmaxTime['EDATE']." ".$arrmaxTime['ETIME']));
//	if (!$Trns || $sqlInsert =="") {
//		$Trns = $commonObj->rollbackTran();
//		echo "<script>alert('Error Uploading Event Logs!')";
//	} else {
//		$Trns = $commonObj->commitTran();
//		echo "alert('Event Logs Successfully Uploaded, latest uploaded log(s): $datetime')";
//	}	
//}
?>