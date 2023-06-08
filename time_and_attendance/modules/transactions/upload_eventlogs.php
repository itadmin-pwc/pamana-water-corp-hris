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

if(isset($_POST['btnUpload'])) {
	if(empty($_POST['cmbbranch']) || $_POST['cmbbranch'] == '0') {
		echo "<script>alert('Please select branch.') </script>";
	}else{
		if ($_FILES["csv_tk"]["size"] > 0) {

			$fileName = $_FILES["csv_tk"]["tmp_name"];
	
			// Get the file extension
			$extension = pathinfo($_FILES["csv_tk"]["name"], PATHINFO_EXTENSION);
	
			// Check if the extension is "csv"
			if ($extension !== "csv") {
				echo "<script>alert('Please upload csv file only.') </script>";
			}else{

				$branch = $_POST['cmbbranch'];

				$sqlGetPayDate = "SELECT brn.brnCode, brn.brnDefGrp, pprd.pdFrmDate, pprd.pdToDate 
							  FROM tblBranch brn
							  INNER JOIN  tblPayPeriod pprd ON brn.compCode = pprd.compCode 
							  WHERE brn.brnCode='$branch' AND pprd.pdTSStat='O' AND pprd.payCat='3'";
				$resPayPeriod = $commonObj->getSqlAssocI($commonObj->execQryI($sqlGetPayDate));
				$frDate = date('Ymd',strtotime($resPayPeriod['pdFrmDate']));
				$toDate = date('Ymd',strtotime($resPayPeriod['pdToDate'])); 
				$_SESSION['toDate'] = $toDate;
				//		and len(ETAG)>=$BioLength	$sqlDelete = "Delete from tblTK_EventLogs where cStoreNum='{$_GET['brnCode']}' AND (EDATE>='". $frDate."' AND EDATE<='".$toDate."') and len(ETAG)<8";
				$BioLength = ($_GET['brnCode'] == 1)? 4:5;
				$sqlDelete = "Delete from tblTK_EventLogs where (EDATE>='". $frDate."' AND EDATE<='".$toDate."') ";
				$commonObj->execQry($sqlDelete);

				$file = fopen($fileName, "r");
				$headers = fgetcsv($file, 1000, ",");
		
				while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
					$empBio = $data[0]; // Bio Number
					$empNo = $data[1]; // Empt Number
					$name = $data[2]; // Name
					$datetime = $data[3]; // Third column value (Time)
					$xType = $data[6];

					if ($xType === 'Invalid') {
						continue; // Skip the current iteration and move to the next row
					}
					
					// $state = $data[4]; // Fourth column value (State)
		
					// // Do something with the values
				
					// echo "AC-No.: $acNo<br>";
					// echo "Emp-No.: $EmpNO<br>";
					// echo "Name: $name<br>";
					// echo "Time: $time<br>";
					// echo "State: $state<br>";

					//die(); ///*************************Uncomment for debugging*************/////

					// Separate date and time
					$date = date('Y-m-d', strtotime($datetime));
					$time = date('His', strtotime($datetime));

					// Convert date to desired format (20230603)
					$dateFormatted = date('Ymd', strtotime($date));

					// Convert time to desired format (120100)
					$timeFormatted = date('His', strtotime($time));

					// GRACE PERIOD
					$grace_start = "080000";
					$grace_end = "081500";

					if ($timeFormatted >= $grace_start && $timeFormatted <= $grace_end) {
						$timeFormatted = $grace_start;
					}
					// END GRACE PERIOD

					$datebio=$dateFormatted;
					$timebio =$timeFormatted;
					$bionum=$empBio;

					$checkifexist="select EDATE,ETIME,ETAG from tblTK_EventLogs where EDATE = '$date' and ETIME='$time' and ETAG='$bionum'";
					$checklogifexist = $commonObj->execqrybio($checkifexist);
					$rowcountbiolog =$checklogifexist->num_rows;
					if($rowcountbiolog == 0){

						$sqlInsert = " Insert into tblTK_EventLogs (cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT) 
										values (
													'{$branch}',
													'{$datebio}',
													'{$timebio}',
													'',
													'',
													'{$empNo}',
													'{$bionum}',	
													'{$name}',
													'',
													'',
													'',
													'',
													'',
													''
													)";
						$commonObj->execQryI($sqlInsert);			
					
					}
				}

				$arrmaxTime = $commonObj->getSqlAssocI($commonObj->execQryI("SELECT EDATE,ETIME from tblTK_EventLogs order by EDATE desc limit 1"));
				$datetime = date('Y-m-d H:i',strtotime($arrmaxTime['EDATE']." ".$arrmaxTime['ETIME']));

				echo "<script>alert('Event Logs Successfully Uploaded, latest uploaded log(s): $datetime') </script>";
			}
		}else{
			echo "<script>alert('Please choose a valid csv file.') </script>";
		}
	}
}

switch($_GET['act']) {
	case 'upload' :
		echo 'tae tae';
		print_r($_FILES["csv_tk"]);
		$fileName = $_FILES["csv_tk"]["tmp_name"];

		echo $fileName;

		if ($_FILES["csv_tk"]["size"] > 0) {

			$file = fopen($fileName, "r");
			$row = 0;

			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
				print_r($data);
			//   $row += 1;
			// 	if($row <> 0) {
			// 		$values = '';

			// 		for($i = 0; $i < 21; $i++) {
			// 			if($i == 5) {
			// 				$column[$i] = !empty($column[$i]) ? date("Y-m-d H:i:s",strtotime(str_replace('/','-',$column[$i]))) : null;
			// 			}
			// 			$values .= "'{$column[$i]}'";
			// 			if($i <> 20) {
			// 				$values .= ',';
			// 			}
			// 		}

			// 		$sql = "INSERT INTO people(first_name,middle_name,last_name,gender,age,tbl_bday,house_no,street,purok,city,province,civil_status,owner,remarks,affiliation,pets,educational,bussiness_name,age_group,profesion_job, place_birth)
			// 		values ({$values})";

			// 		$result = $conn->query($sql);

			// 		if (!empty($result)) {
			// 			$success_msg = "CSV Data successfully imported into the Database";
			// 		} else {
			// 			$type = "error";
			// 			echo $error_msg = "Problem in Importing CSV Data <br>" . mysqli_error($conn);
			// 		}
			// 	}
			}
		}

		die();
				echo $sqlGetPayDate = "SELECT brn.brnCode, brn.brnDefGrp, pprd.pdFrmDate, pprd.pdToDate 
							  FROM tblBranch brn
							  INNER JOIN  tblPayPeriod pprd ON brn.compCode = pprd.compCode 
							  WHERE brn.brnCode='0001' AND pprd.pdTSStat='O' AND pprd.payCat='3'";	
			$resPayPeriod = $commonObj->getSqlAssocI($commonObj->execQryI($sqlGetPayDate));
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
	<form name="uploadTimeLogs" id="uploadTimeLogs" method="post" enctype="multipart/form-data" action="<? echo $_SERVER['PHP_SELF']; ?>">
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
            <td width="20%" height="25" class="style1 style2">Branch</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2">
				<? $commonObj->DropDownMenu($commonObj->makeArr($commonObj->getAllBranch(),'brnCode','brnDesc',''),'cmbbranch',$commonObj->branch,'class="inputs" style="width:222px;"'); ?>
            </td>
          </tr>

		  <tr>
            <td width="20%" height="25" class="style1 style2">CSV File</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5">
				<input type="file" accept=".csv" name="csv_tk" id="csv_tk">
			</td>
          </tr>
          
		  <tr>

		    <td height="25" colspan="7" class="childGridFooter">
							<div align="center">
							  <input name="btnUpload" type="submit" class="inputs" id="btnProcess" value="Upload Event Logs">
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
	function ProcessTS(e) {
		timedCount();
		$('btnProcess').disabled=true;

		params = 'upload_eventlogs.php?act=upload';
		new Ajax.Request(params,{
			method : 'post',
			parameters : $('uploadTimeLogs').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			}	
		});
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





	die("hello");
			$frDate = $_GET['frDate'];
			$conn =odbc_connect("hris","sa","password01")or die('MSSQL ERROR');
			// $conn = odbc_connect("Driver={SQL Server Native Client 10.0};Server=192.168.0.97;Database=MYSCTAowh;","sa","password01");

			//"Driver={SQL Server Native Client 10.0};Server=192.168.0.97;Database=MYSCTAowh;","sa","password01"
$checkbio ="select bioNumber  from tblbioemp where empNo in(SELECT empNo from tblempmast where empStat <>'RS')";
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

// BIOMETRIC AND HRIS RELATIONS 
// CODE STARTS HERE....
			$res = odbc_exec($conn,"SELECT TimeLogs.RecordDate as EDATE, replace(convert(varchar, RecordTime, 108),':','') AS ETIME,
								TimeLogs.TerminalID as EDOOR, Null AS EFLOOR, TimeLogs.EmployeeID as ESABUN, Employees.AccessNo as ETAG, 
								Null as ENAME, Null as ELNAME, Null AS EPART, Null AS EDEP, Null AS ESTATUS,
								Null AS EFUNCTION, Null AS EINOUT, TimeLogs.RecordDate, TimeLogs.RecordTime, Employees.IsInactive 
							 FROM Employees 
							 INNER JOIN TimeLogs ON Employees.EmployeeID = TimeLogs.EmployeeID
							 WHERE   Employees.AccessNo = '{$num}' AND TimeLogs.RecordDate BETWEEN '".date('m/d/Y',strtotime($frDate))."' AND '".date('m/d/Y')."'") ;
			
		
			if( ! odbc_num_rows( $res ) ) {
$res = "";
$sqlInsert = "";
   
} else{
	
			while($data = odbc_fetch_array($res) ) {
			$datebio=date('Ymd',strtotime($data['EDATE']));
			$timebio=$data['ETIME'];
			$bionum=$data['ETAG'];

			$checkifexist="select EDATE,ETIME,ETAG from tblTK_EventLogs where EDATE = '$datebio' and ETIME='$timebio' and ETAG='$bionum'";
    		$checklogifexist = $commonObj->execqrybio($checkifexist);
    		$rowcountbiolog =$checklogifexist->num_rows;
    		if($rowcountbiolog == 0){

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
				$commonObj->execQryI($sqlInsert);			
			
}		
			}}
	

			}
			
			}  

	$arrmaxTime = $commonObj->getSqlAssocI($commonObj->execQryI("SELECT EDATE,ETIME from tblTK_EventLogs order by EDATE desc limit 1"));
	$datetime = date('Y-m-d H:i',strtotime($arrmaxTime['EDATE']." ".$arrmaxTime['ETIME']));

		echo "<script>alert('Event Logs Successfully Uploaded, latest uploaded log(s): $datetime') </script>";

}
?>