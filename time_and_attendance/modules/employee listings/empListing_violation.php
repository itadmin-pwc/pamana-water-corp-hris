<?
##################################################

session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("employee_listings.obj.php");


$violationProoflistObj = new employeeListings();
$sessionVars = $violationProoflistObj->getSeesionVars();
$violationProoflistObj->validateSessions('','MODULES');

$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblTK_UserBranch tblUB, tblBranch as tblbrn
							where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'
							order by brnDesc";
		
$resBrnches = $violationProoflistObj->execQry($queryBrnches);
$arrBrnches = $violationProoflistObj->getArrRes($resBrnches);
$arrBrnch = $violationProoflistObj->makeArr($arrBrnches,'brnCode','brnDesc','All');



if(isset($_POST['btnUpload'])) {
	$rep_fileName = "empListing_violation_pdf.php?&branchCode=".$_POST['brnCode']."&vioCode=".$_POST["vioCode"]."";
	//echo $_GET['brnCode'].""."<br>".$rep_fileName ;
}

?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='ts.js'></script>
</HEAD>
	<BODY>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
    <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
    		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
    			&nbsp; Employee Listing of Violation Types
    		</td>
    	</tr>
    
    	<tr>
    		<td></td>
    	</tr>
    
    	<tr>
    		<td class="parentGridDtl" >
    			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                	<tr> 
                        <td class="gridDtlLbl" width="20%">Branch </td>
                        <td class="gridDtlLbl" width="1%">:</td>
                        <td class="gridDtlVal"> 
                            <? 					
                             	$violationProoflistObj->DropDownMenu($arrBrnch,'brnCode',$brnCode,'class="inputs"');
							 ?>
                        </td>
                    </tr>
                    
                	<tr> 
                        <td class="gridDtlLbl" width="20%">Select Violation Type </td>
                        <td class="gridDtlLbl" width="1%">:</td>
                        <td class="gridDtlVal"> 
                            <? 					
                             	$arrViolation = $violationProoflistObj->makeArr($violationProoflistObj->getListVio(),'violationCd','violationDesc','All');
                                $violationProoflistObj->DropDownMenu($arrViolation,'vioCode',$vioCode,"");
							 ?>
                        </td>
                    </tr>
                    
                    
                    
                    <tr>
                    	<td colspan="3" align="center"><input name="btnUpload" type="submit" id="btnUpload" value="Generate Report" class="inputs"></td>
                    </tr>
    			</table>
    			<br>
    			<iframe src="<?php echo $rep_fileName; ?>" height="380px;" width="99%">
                	 
                </iframe>
               
    		</td>
    	</tr> 
    	<tr > 
    		<td class="gridToolbarOnTopOnly" colspan="6">
    			<CENTER>
    				<input style="background-color:#fff; height:18px; text-align: center;  border:0px solid;" >
    			</CENTER>	
    		</td>
    	</tr>
    </table>
</form>
</BODY>
</HTML>
