<?
##################################################

session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("empmast_migration.obj.php");


$migEmpMastObj = new migEmpMastObj();
$sessionVars = $migEmpMastObj->getSeesionVars();
$migEmpMastObj->validateSessions('','MODULES');


if(isset($_POST['btnUpload'])) 
{
	switch($_POST["reportType"])
	{
		case "13THMONCON":
			$rep_fileName = "consolidation_report_13thMonth_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
		
		case "EARNINGS":
			$rep_fileName = "consolidation_report_earnings_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
		
		case "MTDPAYSUMYTD":
			$rep_fileName = "consolidation_report_mtdpaysum_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
		
		case "TRANEMP":
			$rep_fileName = "consolidation_report_earnings_transfer_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
		
		case"ALPHA":
			$rep_fileName = "consolidation_report_yearend_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
		
		case "PAYREGTBL":
			$rep_fileName =  "consolidation_report_consolidation_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
		
		case "TAXREFUNDDTL":
			$rep_fileName =  "consolidation_report_taxrefund_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
		
		case "TAXREFUNDSUM":
			$rep_fileName =  "consolidation_report_taxrefundsum_pdf.php?&empBrnCode=".$_POST["empBrnCode"]."&pdYear=".$_POST["empYear"];
		break;
	}
	//
}


?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/main_emp_loans.css');</style>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='../transactions/timesheet_js.js'></script>
</HEAD>
	<BODY>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
    <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
    		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
    			&nbsp;Consolidation Report
    		</td>
    	</tr>
    
    	<tr>
    		<td></td>
    	</tr>
    
    	<tr>
    		<td class="parentGridDtl" >
    			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                	 <tr> 
                        <td class="gridDtlLbl">Report Type </td>
                        <td class="gridDtlLbl">:</td>
                        <td class="gridDtlVal"> 
                            <? 
								//if($_SESSION["user_level"]=='1') 
                                	$migEmpMastObj->DropDownMenu(array('13THMONCON'=>'13TH MONTH CONSOLIDATION REPORT', 'EARNINGS'=>'EARNINGS DETAIL AND PAYSUMMARY FOR 13TH MONTH','MTDPAYSUMYTD'=>'MTD VS. PAYSUM. VS. YTD', 'TRANEMP'=>'TRANSFER EMPLOYEES','ALPHA'=>'ALPHALIST DETAIL', 'PAYREGTBL'=>'PAY-REGISTERS VS. TABLES',  'TAXREFUNDDTL'=>'TAX REFUND DETAIL REPORT', 'TAXREFUNDSUM'=>'TAX REFUND SUMMARIZE REPORT'),'reportType',$reportType,$reportType_dis); 
                           		//else
									//$migEmpMastObj->DropDownMenu(array('13THMONCON'=>'13TH MONTH CONSOLIDATION REPORT','MTDPAYSUMYTD'=>'MTD VS. PAYSUM. VS. YTD', 'TRANEMP'=>'TRANSFER EMPLOYEES', 'TAXREFUNDDTL'=>'TAX REFUND DETAIL REPORT', 'TAXREFUNDSUM'=>'TAX REFUND SUMMARIZE REPORT'),'reportType',$reportType,$reportType_dis); 
                           	
						    ?>
                        </td>
                    </tr>
                    
                        
                    <tr> 
                        <td class="gridDtlLbl">Branch </td>
                        <td class="gridDtlLbl">:</td>
                        <td class="gridDtlVal"> 
                            <? 					
                                $arrBranch = $migEmpMastObj->makeArr($migEmpMastObj->getBrnchArt($compCode),'brnCode','brnDesc','');
                                $migEmpMastObj->DropDownMenu($arrBranch,'empBrnCode',$empBrnCode,$empBrnCode_dis);
                            ?>
                        </td>
                    </tr>
                    <tr>
                      <td class="gridDtlLbl">Year</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><? 					
                                $arrYear = $migEmpMastObj->makeArr($migEmpMastObj->getPdYearList(),'pdYear','pdYear','');
                                $migEmpMastObj->DropDownMenu($arrYear,'empYear',$empYear,$empBrnCode_dis);
                            ?></td>
                    </tr>
                        
                      
                    <tr> 
                       
                        <td class="gridDtlVal" align="center" colspan="4"> 
                        	
                        		<input name="btnUpload" type="submit" id="btnUpload" value="Generate Report" class="inputs">
                                
                            
                        </td>
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
    				<input style="background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" >
    			</CENTER>	
    		</td>
    	</tr>
    </table>
</form>
</BODY>
</HTML>