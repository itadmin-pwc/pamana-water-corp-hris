<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("common_obj.php");

$payrollTypeObj = new inqTSObj();
$sessionVars = $payrollTypeObj->getSeesionVars();
$payrollTypeObj->validateSessions('','MODULES');

$fileName = $_GET['fileName'];
$empBrnCode = $_GET['empBrnCode'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$empNo = $_GET['empNo'];
$shiftCode = $_GET['shiftCode'];
$payGrp = $_GET['payGrp'];
$reportPath = 'employees_timesheet_pdf.php?empBrnCode='.$empBrnCode.'&fileName='.$fileName.'&empDept='.$empDept.'&empDiv='.$empDiv.'&empSect='.$empSect.'&empNo='.$empNo.'&shiftCode='.$shiftCode.'&payGrp='.$payGrp.'"';

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='timesheet_js.js'></script>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
	</HEAD>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
                    		&nbsp;EMPLOYEES TIMESHEET</td>
			  </tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="27" class="gridToolbar" align="">
								<input name="back" type="button" id="back" value="Back" class="inputs" onClick="location.href='employees_timesheet.php';">
              				</td>
							
                            <tr>
                            	<td>
                                	<iframe src="<?php echo $reportPath; ?>" height="380px;" width="99%">
                                    </iframe>
                            	</td>
                            </tr>
							
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		
	</BODY>
</HTML>
