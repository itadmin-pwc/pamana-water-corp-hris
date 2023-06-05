<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$empBrnCode = $_GET['empBrnCode'];
$monthfr =  $_GET["monthfr"];
$monthto =  $_GET["monthto"];

$reportPath = 'emplist_minWage_pdf.php?&compCode='.$empBrnCode.'&monthto='.$monthto.'&monthfr='.$monthfr.'"';

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='timesheet_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
                    		&nbsp;Generate Employee List of Updated Minimum Wage Tag
                    </td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="27" class="gridToolbar" align="">
								<input name="back" type="button" id="back" value="Back" onClick="location.href='emplist_minWage.php';">
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
