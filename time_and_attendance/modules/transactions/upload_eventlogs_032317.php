<?
##################################################
session_start(); 
$domain = $_SERVER['REMOTE_ADDR'];

//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
$commonObj = new commonObj();
$sessionVars = $commonObj->getSeesionVars();
$commonObj->validateSessions('','MODULES');

switch($_GET['act']) {
	case 'proc' :
	
//			$sqlGet = "Select max(CAST(EDATE AS int)) as EDATE from tblTK_EventLogs where cStoreNum='{$_GET['brnCode']}'";
//			$result = $commonObj->getSqlAssoc($commonObj->execQry($sqlGet));
//			switch($result['EDATE']) {
//				case date('Ymd'):
//					$frDate =(int)$result['EDATE']-1;
//				break;
//				case "":
//					$frDate = date('m/d/Y');
//					$sqlGetDate = "Select Dateadd(Day,-40,'$frDate') as curdate";
//					$resDate = $commonObj->getSqlAssoc($commonObj->execQry($sqlGetDate));	
//					$frDate = date('Ymd',strtotime($resDate['curdate']));
//				break;
//				default:
//					$frDate =(int)$result['EDATE'];
//			}
//			$sqlDelete = "Delete from tblTK_EventLogs where cStoreNum='{$_GET['brnCode']}' AND EDATE>='". $frDate."'";
				echo $sqlGetPayDate = "SELECT brn.brnCode, brn.brnDefGrp, pprd.pdFrmDate, pprd.pdToDate 
							  FROM tblBranch brn
							  INNER JOIN  tblPayPeriod pprd ON brn.compCode = pprd.compCode 
							  WHERE brn.brnCode='0001' AND pprd.pdTSStat='O' AND pprd.payCat='3'";	
			$resPayPeriod = $commonObj->getSqlAssoc($commonObj->execQry($sqlGetPayDate));
			$frDate = date('Ymd',strtotime($resPayPeriod['pdFrmDate']));
			$toDate = date('Ymd',strtotime($resPayPeriod['pdToDate'])); 
			$_SESSION['toDate'] 	= $toDate;
//		and len(ETAG)>=$BioLength	$sqlDelete = "Delete from tblTK_EventLogs where cStoreNum='{$_GET['brnCode']}' AND (EDATE>='". $frDate."' AND EDATE<='".$toDate."') and len(ETAG)<8";
			$BioLength = ($_GET['brnCode'] == 1)? 4:5;
			$sqlDelete = "Delete from tblTK_EventLogs where (EDATE>='". $frDate."' AND EDATE<='".$toDate."') ";
			$commonObj->execQry($sqlDelete);
			
			header("Location: Upload_eventLogs.php?act=Insert&frDate=$frDate");
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
            <td width="20%" height="25" class="style1 style2">Company</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2"><font class="gridDtlLblTxt" id="compCode">
              <?
										$compName = $commonObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
            </font></span></td>
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
		timedCount();
		$('btnProcess').disabled=true;
		location.href='<?=$_SERVER['PHP_SELF']?>?brnCode=<?=$_GET['brnCode']?>&act=proc';
		
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
if ($_GET['act']=='Insert') {
			$frDate = $_GET['frDate'];
			$conn = odbc_connect("hris","sa","password01");

			//$res = odbc_exec($conn,"SELECT TimeLogs.RecordDate as EDATE, replace(convert(varchar, RecordTime, 108),':','') AS ETIME,
			//					TimeLogs.TerminalID as EDOOR, Null AS EFLOOR, TimeLogs.EmployeeID as ESABUN, Employees.AccessNo as ETAG, 
			//					Employees.FirstName as ENAME, Employees.LastName as ELNAME, Null AS EPART, Null AS EDEP, Null AS ESTATUS,
			//					Null AS EFUNCTION, Null AS EINOUT, TimeLogs.RecordDate, TimeLogs.RecordTime, Employees.IsInactive 
			//				 FROM Employees 
			//				 INNER JOIN TimeLogs ON Employees.EmployeeID = TimeLogs.EmployeeID
			//				 WHERE TimeLogs.RecordDate BETWEEN '".date('m/d/Y',strtotime($frDate))."' AND '".date('m/d/Y')."'");
		//
			//echo "SELECT TimeLogs.RecordDate as EDATE, replace(convert(varchar, RecordTime, 108),':','') AS ETIME,
			//					TimeLogs.TerminalID as EDOOR, Null AS EFLOOR, TimeLogs.EmployeeID as ESABUN, Employees.AccessNo as ETAG, 
			//					Null as ENAME, Null as ELNAME, Null AS EPART, Null AS EDEP, Null AS ESTATUS,
			//					Null AS EFUNCTION, Null AS EINOUT, TimeLogs.RecordDate, TimeLogs.RecordTime, Employees.IsInactive 
			//				 FROM Employees 
			//				 INNER JOIN TimeLogs ON Employees.EmployeeID = TimeLogs.EmployeeID
			//				 WHERE TimeLogs.RecordDate BETWEEN '".date('m/d/Y',strtotime($frDate))."' AND '".date('m/d/Y')."'";
$checkbio ="select bioNumber  from tblbioemp";
    	$chkresbio = $commonObj->execqrybio($checkbio);
    	$rowcountbio =$chkresbio->num_rows;
    	$bum=$commonObj->getArrResloadbio($chkresbio);
    	//$i ="";
    	$dateto = $_SESSION['toDate'];
    	if($rowcountbio > 0){
    		for($i = 0;$i<=$rowcountbio;$i++){
    			$num="";
    			$num= $bum[$i]['bioNumber'];
    			$res = "";
$sqlInsert = "";
			$res = odbc_exec($conn,"SELECT TimeLogs.RecordDate as EDATE, replace(convert(varchar, RecordTime, 108),':','') AS ETIME,
								TimeLogs.TerminalID as EDOOR, Null AS EFLOOR, TimeLogs.EmployeeID as ESABUN, Employees.AccessNo as ETAG, 
								Null as ENAME, Null as ELNAME, Null AS EPART, Null AS EDEP, Null AS ESTATUS,
								Null AS EFUNCTION, Null AS EINOUT, TimeLogs.RecordDate, TimeLogs.RecordTime, Employees.IsInactive 
							 FROM Employees 
							 INNER JOIN TimeLogs ON Employees.EmployeeID = TimeLogs.EmployeeID
							 WHERE   Employees.AccessNo = '{$num}' AND TimeLogs.RecordDate BETWEEN '".date('m/d/Y',strtotime($frDate))."' AND '".date('m/d/Y')."'") ;
			
			
			//$odbcrows=odbc_num_rows($res);
			//if($odbcrows>0){
			if( ! odbc_num_rows( $res ) ) {
$res = "";
$sqlInsert = "";
   //$nll=false;false !==(
} else{
	$deleteexist = "delete from tblTK_EventLogs where EDATE>= ";
			while( $data = odbc_fetch_array($res) ) {
			
				
    		
				$sqlInsert = " Insert into tblTK_EventLogs (cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT) 
								values (
											'{$data['cStoreNum']}',
											'".date('Ymd',strtotime($data['EDATE']))."',
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
											)";
				$commonObj->execQry($sqlInsert);			
/*			
							if ($sqlInsert == "") {
								$sqlInsert .= " (
											'{$data['cStoreNum']}',
											'".date('Ymd',strtotime($data['EDATE']))."',
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
											)" ;
							} else {
							$sqlInsert .=",(
											'{$data['cStoreNum']}',
											'".date('Ymd',strtotime($data['EDATE']))."',
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
											)";
							}
*/				
//}			
			}}
		//	if( ! odbc_num_rows( $res ) ) {

   //$nll=false;
//} 


			}
			
			}  
//			if ($sqlInsert!="") {
//				$sqlInsert = " Insert into tblTK_EventLogs (cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT) values $sqlInsert";
//				$commonObj->execQry($sqlInsert);
//			}
	

	$arrmaxTime = $commonObj->getSqlAssoc($commonObj->execQry("SELECT EDATE,ETIME from tblTK_EventLogs order by EDATE desc limit 1"));
	$datetime = date('Y-m-d H:i',strtotime($arrmaxTime['EDATE']." ".$arrmaxTime['ETIME']));
	//if ($sqlInsert=="") {
		//echo " <script>alert('Error Uploading Event Logs! ')</script>";
	//} else {
		echo "<script>alert('Event Logs Successfully Uploaded, latest uploaded log(s): $datetime') </script>";
	//}
	
}
?>