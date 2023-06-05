<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("movement_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');


$cmbDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$pafType = $_GET['pafType'];
$type = $_GET['type'];
$code = $_GET['code'];
$fromdt = $_GET['from'];
$todt = $_GET['to'];


$arrReason = $inqTSObj->getReasonCd($code,$_SESSION["company_code"]);
$reportPath = 'salary_list_pdf.php?empDiv='.$cmbDiv.'&empDept='.$empDept.'&empSect='.$empSect.'&pafType='.$pafType.'&type='.$type.'&code='.$code.'&from='.$fromdt.'&to='.$todt.'"';

?>

<HTML>
<head>
	<script type='text/javascript' src='movement.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
                    		&nbsp;SALARY INCREASE REPORT BY <?=strtoupper($arrReason["reasonDesc"])?></td>
			  </tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="27" class="gridToolbar" align="">
								<input name="back" type="button" id="back" value="Back" onClick="location.href='salary.php';">
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
