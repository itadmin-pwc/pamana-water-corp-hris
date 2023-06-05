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
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empPos = $_GET['empPos'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$txtSearch = $_GET["txtSearch"];
$srchType = $_GET["srchType"];
$monthfr =  $_GET["monthfr"];
$monthto =  $_GET["monthto"];

$reportPath = 'blacklist_list_pdf.php?&empBrnCode='.$empBrnCode.'&monthto='.$monthto.'&monthfr='.$monthfr.'&srchType='.$srchType.'&txtSearch='.$txtSearch.'&fileName='.$fileName.'&optionId='.$optionId.'&hide_empDept='.$hide_empDept.'&empPos='.$empPos.'&empDept='.$empDept.'&empDiv='.$empDiv.'"';

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
                    		&nbsp;EMPLOYEE BLACKLIST INFORMATION
                    </td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="27" class="gridToolbar" align="">
								<input name="back" type="button" id="back" value="Back" onClick="location.href='blacklist.php';">
              				</td>
							
                            <tr>
                            	<td>
                                	<iframe src="<?php echo $reportPath; ?>" height="380px;" width="99%">
                                    </iframe>
                            	</td>
                            </tr>
							<tr>
								<td colspan="27" align="center" class="childGridFooter">
									<? //$pager->_viewPagerButton("allowance_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&groupType=".$groupType."&orderBy=".$orderBy."&catType=".$catType."&payPd=".$payPd."&repType=$_GET[repType]&tbl=$tbl");?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		
	</BODY>
</HTML>
