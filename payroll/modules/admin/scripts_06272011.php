<?
##################################################

session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("scripts.obj.php");

$scriptsObj = new scriptObj();
$sessionVars = $scriptsObj->getSeesionVars();
$scriptsObj->validateSessions('','MODULES');



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
<form action="<? echo $_SERVER['../transactions/PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
    <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
    		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
    			&nbsp;Generate Script
    		</td>
    	</tr>
    
    	<tr>
    		<td></td>
    	</tr>
    
    	<tr>
    		<td class="parentGridDtl" >
    			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
    					
                       <tr> 
                            <td class="gridDtlLbl">Scripts </td>
                            <td class="gridDtlLbl">:</td>
                            <td class="gridDtlVal"> 
                                <?  
                                    $scriptsObj->DropDownMenu(array('PSYTD'=>'PAY SUMMARY vs. YTD','TST'=>'TEST TBLTK_TIMESHEET', 'SHIFT'=>'SHIFT CODE DTL AND HDR','MIGRATE'=>'MIGRATE PAYSUM, YTD', 'MIGBIO'=>'MIGRATE BIO-DATA','MIGEMP'=>'MIGRATE EMP. OF PPCI TO JR.', 'SPERTBL'=>'SIZE PER TABLES', 'AGELIST'=>'AGE MANPOWER COUNT', '13TH'=>'ESTIMATED 13TH MONTH', '2316'=>'2316 DATA FOR RESIGNED EMPLOYEES', 'INSTBLEARN'=>'INSERT TO TBLEARNINGS', 'UPDATEPAYSUMADVANCE'=>'UPDATE PAYROLLSUMMARY - SPRT.ADVANCE', 'UPDATEYTDDATAHISTADVANCE'=>'UPDATE YTD DATA HIST. - SPRT.ADVANCE', 'UPDATEYTDDATABASIC'=>'UPDATE YTD DATA HIST. - BASIC RECLASS', 'UPDATEYTDDATAALLOW'=>'UPDATE YTD DATA HIST. -  ADVANCE RECLASS', 'TAXCHK'=>'TAX CHECKED', 'TRANSFER'=>'TRANSFER EMPLOYEES DATA', 'CHKMAMCINDY'=>'CHK. PAYSUM MAM CINDY', 'EMPLIST'=>'MAM SHARON REPORT', 'YTDCONSOL'=>'TABLE CONSOLIDATION', 'PPCIJR'=>'PPCI TO JR.', 'UPDTEYTDTAXREFUND'=>'UPDATE YTDDATA OF 2010 OF EMPLOYEES WITH TAX REFUND', 'MIGJRBRANCH'=>'MIGRATE LIST OF BRANCH', 'GETPASS'=>'GET USER ACCESS - PASSWORD','GLDEPTUPDATE'=>'UPDATE DEPT. IN GL CODE', 'UPDATELOANS'=>'UPDATE LOAN PRINCIPAL','CHCKLEGRESTDAY'=>'CHECK LEGAL ON A RESTDAY', 'COMPARADOXHRIS'=>'COMPARE PARADOX AND HRIS SSSLOAN TEXTFILE', 'UPDATETBLEMPLOANS'=>'UPDATE LOANS'),'scriptType',$scriptType,$scriptType_dis); 
                                ?>
                            </td>
                        </tr>
                        
                       
    			</table>
    			<br>
    			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
                  	<tr>
                    	<td>
                        	<CENTER>
                				<input name="btnGenerate" type="submit" id="btnGenerate" value="Generate Script" class="inputs">
                			</CENTER>
                    	</td>
                  	</tr>
    			</table> 
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

<?php
if(isset($_POST['btnGenerate'])) {
		
		switch($_POST["scriptType"])
		{
			case "PSYTD":
				echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
					echo "<tr style='height:30px;' align='center'>";
						echo "<td width='25%'>Current tblPayrollSummary</td>";
						echo "<td width='25%'>tblPayrollSummary Migrated</td>";
						echo "<td width='25%'>Current tblYtdData</td>";
						echo "<td width='25%'>Current tblGovAdded</td>";
					echo "</tr>";
					
					echo "<tr>";
						echo "<td>";
							echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
								echo "<tr style='height:30px;' align='center'>";
									echo "<td width='25%'>pdNum</td>";
									echo "<td width='25%'>EmpNo</td>";
									echo "<td width='25%'>GrossInc</td>";
									echo "<td width='25%'>TaxInc</td>";
								echo "</tr>";
							echo "</table>";
						echo "</td>";
						
						echo "<td>";
							echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
								echo "<tr style='height:30px;' align='center'>";
									echo "<td>pdNum</td>";
									echo "<td>EmpNo</td>";
									echo "<td>GrossInc</td>";
									echo "<td>TaxInc</td>";
								echo "</tr>";
							echo "</table>";
						echo "</td>";
						
						echo "<td>";
							echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
								echo "<tr style='height:30px;' align='center'>";
									echo "<td width='50%'>EmpNo</td>";
									echo "<td width='50%'>YtdTaxable</td>";
								echo "</tr>";
							echo "</table>";
						echo "</td>";
						
						echo "<td>";
							echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
								echo "<tr style='height:30px;' align='center'>";
									echo "<td width='20%'>MonthPeriodDate</td>";
									echo "<td width='20%'>AmtTotal</td>";
									echo "<td width='20%'>MonthToDed</td>";
									echo "<td width='20%'>AmountToDed</td>";
									echo "<td width='20%'>AddStat</td>";
								echo "</tr>";
							echo "</table>";
						echo "</td>";
					echo "</tr>";
					
					$arrEmpNo = $scriptsObj->getEmp();
					foreach($arrEmpNo as $arrEmpNo_val)
					{
						$sumTaxable= 0;
						echo "<tr>";
							echo "<td valign='top'>";
								$arrPaySumHist = $scriptsObj->paySumHist($arrEmpNo_val["empNo"]);
								echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
									foreach($arrPaySumHist as $arrPaySumHist_val)
									{
										echo "<tr style='height:30px;' >";
											echo "<td width='25%' align='center'>".$arrPaySumHist_val["pdNumber"]."</td>";
											echo "<td width='25%' align='center'>".$arrPaySumHist_val["empNo"]."</td>";
											echo "<td width='25%' align='right'>".$arrPaySumHist_val["grossEarnings"]."</td>";
											echo "<td width='25%' align='right'>".$arrPaySumHist_val["taxableEarnings"]."</td>";
										echo "</tr>";
										
										$sumTaxable+=$arrPaySumHist_val["taxableEarnings"];
									}
									echo "<tr style='height:30px;' >";
											echo "<td width='25%' align='center' colspan='3'>TOTAL</td>";
											echo "<td width='25%' align='center' >".$sumTaxable."</td>";
									echo "</tr>";
								echo "</table>";
							echo "</td>";
							
							echo "<td valign='top'>";
								$arrPaySumHistMig = $scriptsObj->paySumHistMig($arrEmpNo_val["empNo"]);
								echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
									foreach($arrPaySumHistMig as $arrPaySumHistMig_val)
									{
										echo "<tr style='height:30px;'>";
											echo "<td width='25%' align='center'>".$arrPaySumHistMig_val["pdNumber"]."</td>";
											echo "<td width='25%' align='center'>".$arrPaySumHistMig_val["empNo"]."</td>";
											echo "<td width='25%' align='right'>".$arrPaySumHistMig_val["grossEarnings"]."</td>";
											echo "<td width='25%' align='right'>".$arrPaySumHistMig_val["taxableEarnings"]."</td>";
										echo "</tr>";
									}
								echo "</table>";
							echo "</td>";
							
							echo "<td valign='top'>";
								$arrYtdData = $scriptsObj->payYtdData($arrEmpNo_val["empNo"]);
								echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
									foreach($arrYtdData as $arrYtdData_val)
									{
										echo "<tr style='height:30px;'>";
											echo "<td width='50%' align='center'>".$arrYtdData_val["empNo"]."</td>";
											echo "<td width='50%' align='right'>".$arrYtdData_val["YtdTaxable"]."</td>";
										echo "</tr>";
									}
								echo "</table>";
							echo "</td>";
							
							echo "<td valign='top'>";
								$arrGovAdded = $scriptsObj->tblGovAdded($arrEmpNo_val["empNo"]);
								$sumGovTaxAdded = 0;
								echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
									foreach($arrGovAdded as $arrGovAdded_val)
									{
										echo "<tr style='height:30px;'>";
											echo "<td width='20%' align='center'>".$arrGovAdded_val["monthPeriodDate"]."</td>";
											echo "<td width='20%' align='right'>".$arrGovAdded_val["amtTotal"]."</td>";
											echo "<td width='20%' align='center'>".$arrGovAdded_val["monthToDed"]."</td>";
											echo "<td width='20%' align='right'>".$arrGovAdded_val["amountToDed"]."</td>";
											echo "<td width='20%' align='center'>".$arrGovAdded_val["addStat"]."</td>";
											
										echo "</tr>";
										$sumGovTaxAdded+=$arrGovAdded_val["amountToDed"];
									}
									
									echo "<tr style='height:30px;'>";
										echo "<td width='20%' align='center' colspan='4'>GRAND TOTAL</td>";
										echo "<td width='20%' align='center'>".$sumGovTaxAdded."</td>";
									echo "</tr>";
									
									echo "<tr style='height:30px;'>";
										echo "<td width='20%' align='center' colspan='4'>GRAND TOTAL PAY SUMMARY</td>";
										echo "<td width='20%' align='center'>".$sumTaxable."</td>";
									echo "</tr>";
									
									echo "<tr style='height:30px;'>";
										echo "<td width='20%' align='center' colspan='4'>GRAND TOTAL PAY SUMMARY GOV ADDED</td>";
										echo "<td width='20%' align='center'>".($sumGovTaxAdded+$sumTaxable)."</td>";
									echo "</tr>";
								echo "</table>";
								
								$arrLastPayEmpData = $scriptsObj->gettblLastPayDataEmp($arrEmpNo_val["empNo"]);
								echo $arrLastPayEmpData["pdNumber"]."==".$arrLastPayEmpData["payGrp"]."==".$arrEmpNo_val["empStat"];
							echo "</td>";
							
							
						echo "</tr>";	
						
						
					}
					
				echo "</table>";
				
				
			break;
			
			case "TST":
				$qryGetProcTimeSheet = "Select * from tblTk_Timesheet where compCode='".$_SESSION["company_code"]."' 
										and empNo in 
											(Select empNo from tblEmpMast where
												compCode='".$_SESSION["company_code"]."' and 
												empPayCat='".$_SESSION["pay_category"]."' and 
												empPayGrp='".$_SESSION["pay_group"]."' and 
												empStat IN ('RG','PR','CN')
											)
										and tsDate between  '07/24/2010' AND '08/08/2010'
										order by empNo, tsDate";
				
				$arrProcTimeSheet = $scriptsObj->getArrRes($scriptsObj->execQry($qryGetProcTimeSheet));
				
				$regDays = $legDays =  $legDayRestday = $speDayRestday = $cntNoOtNd = $cntNoTimeRelatedDed = $speDayRestday = $cntNoCrossTag= $cntDaysAbsent = 0;
					
				echo "<table width='100%' border='1'>";
					echo "<tr>";
						echo "<td>Employee No</td>";
						echo "<td>Ts Date</td>";
						echo "<td>Day Type</td>";
						echo "<td>Legal Pay Tag</td>";
						echo "<td>App. Type</td>";
						echo "<td>Hrs Worked</td>";
						echo "<td>OT Tag</td>";
						echo "<td>Ded Tag</td>";
						echo "<td>Cross Tag</td>";
					echo "</tr>";
				foreach($arrProcTimeSheet as $arrProcTimeSheet_val)
				{
					
					switch($arrProcTimeSheet_val["dayType"])
					{
						case "01": // Count Regular Day
							if($arrProcTimeSheet_val["hrsWorked"]>=4)
							{
								if($arrProcTimeSheet_val["tsAppTypeCd"]=="")
										$regDays+=1;
								else
								{
									if(($arrProcTimeSheet_val["tsAppTypeCd"]=="12")||($arrProcTimeSheet_val["tsAppTypeCd"]=="13"))
										$regDays+=1;
									elseif(($arrProcTimeSheet_val["tsAppTypeCd"]=="14")||($arrProcTimeSheet_val["tsAppTypeCd"]=="15"))
										$regDays+=0.5;
								}
							}
								
							//Get no. of Days Absent for Monthly Paid Employees : )
							if(($arrProcTimeSheet_val["hrsWorked"]<4) and ($arrProcTimeSheet_val["tsAppTypeCd"]=='08'))
								$cntDaysAbsent+=1;
							
							if(($arrProcTimeSheet_val["hrsWorked"]==4) and (($arrProcTimeSheet_val["tsAppTypeCd"]=='14') || ($arrProcTimeSheet_val["tsAppTypeCd"]=='15')) )
								$cntDaysAbsent+=0.5;
						break;
						
						case "03": // Count Legal Holiday
							if(($arrProcTimeSheet_val["hrsWorked"]>0) and ($arrProcTimeSheet_val["legalPayTag"]=='Y'))
								$legDays+=1;
							
							if(($arrProcTimeSheet_val["hrsWorked"]==0) and ($arrProcTimeSheet_val["legalPayTag"]=='N'))
								$legDays-=1;
						break;
						
						case "05": // Count Legal Holiday Restday
							if($arrProcTimeSheet_val["legalPayTag"]=='Y')
								$legDayRestday+=1;
						break;	
						
						case "06": // Count Special Holiday Restday
							if($arrProcTimeSheet_val["hrsWorked"]>0)
								$speDayRestday+=1;
						break;	
					}
					
					//Get no. of OT/ND
					if($arrProcTimeSheet_val["otTag"]=='Y')
					{
						$cntNoOtNd+=1;
					}
					
					//Get no. of Deduction : Determine if the Employee has time related deductions
					if($arrProcTimeSheet_val["dedTag"]=='Y')
					{
						$cntNoTimeRelatedDed+=1;
					}
					
					//Get no. of Cross tags : Determine if the Employee has a night diff or not (applicable for data controllers)
					if($arrProcTimeSheet_val["crossTag"]=='Y')
					{
						$cntNoCrossTag+=1;
					}
					
					
					echo "<tr>";
						echo "<td>".$arrProcTimeSheet_val["empNo"]."</td>";
						echo "<td>".date("m/d/Y", strtotime($arrProcTimeSheet_val["tsDate"]))."</td>";
						echo "<td>".$arrProcTimeSheet_val["dayType"]."</td>";
						echo "<td>".$arrProcTimeSheet_val["legalPayTag"]."</td>";
						echo "<td>".$arrProcTimeSheet_val["tsAppTypeCd"]."</td>";
						echo "<td>".$arrProcTimeSheet_val["hrsWorked"]."</td>";
						echo "<td>".$arrProcTimeSheet_val["otTag"]."</td>";
						echo "<td>".$arrProcTimeSheet_val["dedTag"]."</td>";
						echo "<td>".$arrProcTimeSheet_val["crossTag"]."</td>";
					echo "</tr>";
					
					if(date("m/d/Y", strtotime($arrProcTimeSheet_val["tsDate"]))==date("m/d/Y", strtotime("08/08/2010")))
					{
						echo "<tr>";
							echo "<td colspan='7'>";
								echo "EMP. NO = ".$arrProcTimeSheet_val["empNo"]."<br>";
								echo "REGULAR DAYS = ".$regDays."<br>";
								echo "LEGAL DAYS = ".$legDays."<br>";
								echo "LEGAL HOLIDAY RESTDAY = ".$legDayRestday."<br>";
								echo "SPECIAL HOLIDAY RESTDAY = ".$speDayRestday."<br>";
								echo "CNT. NO. OF OT = ".$cntNoOtNd."<br>";
								echo "CNT. NO. OF DED. = ".$cntNoTimeRelatedDed."<br>";
								echo "CNT. NO. OF CROSS TAG = ".$cntNoCrossTag."<br>";
								echo "CNT. NO. DAYS ABSENT = ".$cntDaysAbsent."<br>";
								
								$lstTsEmployee.=$arrProcTimeSheet_val["empNo"]."=".$regDays."=".$legDays."=".$legDayRestday."=".$speDayRestday."=".$cntNoOtNd."=".$cntNoTimeRelatedDed."=".$cntNoCrossTag.",";
								
								$regDays = $legDays =  $legDayRestday = $speDayRestday = $cntNoOtNd = $cntNoTimeRelatedDed = $speDayRestday = $cntNoCrossTag = $cntDaysAbsent = 0;
							echo "</td>";
						echo "</tr>";
					}
					
					
					
				}
			//	echo $lstTsEmployee;
				echo "</table>";
				
				$arrEmpList = explode(",", $lstTsEmployee);
				
				foreach($arrEmpList as $arrEmpList_val)
				{
					$exparrEmpList = explode("=", $arrEmpList_val);
					
					
					echo $exparrEmpList[0]."<br>";
				}
			break;
			
			case "SHIFT":
				//Hdr
				$qrygetShiftForHdr = "SELECT     *
									FROM          tblShiftDtl0930$
									
									";
				
				$resgetShiftForHdr = $scriptsObj->execQry($qrygetShiftForHdr);
				$arrgetShiftForHdr = $scriptsObj->getArrRes($resgetShiftForHdr);
				
				$shiftCode = 416;
				foreach($arrgetShiftForHdr as $arrgetShiftForHdrDtl)
				{
					if(($arrgetShiftForHdrDtl["SHIFT_DESC"]!=$shiftDesc)&&($arrgetShiftForHdrDtl["DAY_CODE"]=='1'))
					{
						echo "Insert into tblTK_ShiftHdr(compCode,shiftCode,shiftDesc,shiftLongDesc, status,dateAdded,addedBy) values('2', '".$shiftCode."','".$arrgetShiftForHdrDtl["SHIFT_DESC"]."' ,'".$arrgetShiftForHdrDtl["SHIFT_DESC"]."', 'A', '08/27/2010', '010002408'); <br>";
						//echo "<br>";
						$shiftCode++;
						$test = $shiftCode - 1;
					}
						
						echo "Insert into tblTK_ShiftDtl(compCode,shftCode,dayCode,shftTimeIn,shftLunchOut,shftLunchIn,shftBreakOut,shftBreakIn,shftTimeOut,crossDay,RestDayTag,
										dateAdded,addedBy) 
										  values('".$_SESSION["company_code"]."','".$test."','".$arrgetShiftForHdrDtl["DAY_CODE"]."',
										  '".$arrgetShiftForHdrDtl["TIME_IN"]."','".$arrgetShiftForHdrDtl["LUNCH_OUT"]."',
										  '".$arrgetShiftForHdrDtl["LUNCH_IN"]."','".$arrgetShiftForHdrDtl["BREAK_OUT"]."',
										  '".$arrgetShiftForHdrDtl["BREAK_IN"]."','".$arrgetShiftForHdrDtl["TIME_OUT"]."',
										  '".$arrgetShiftForHdrDtl["CROSSDAY"]."','".$arrgetShiftForHdrDtl["RESTDAY"]."',
										  '08/27/2010','010002408');<br>";
						
					$shiftDesc = $arrgetShiftForHdrDtl["SHIFT_DESC"];
				}
			break;
			
			case "MIGRATE":
				$arr_empInfo = $scriptsObj->getUserInfo($_SESSION["company_code"],'790000001','');
				
				$qrygetPaySum = "SELECT     *
									FROM         fontillasPaySum$ where empNo='790000001'
									";
				
				$resgetPaySum = $scriptsObj->execQry($qrygetPaySum);
				$arrgetpaySum = $scriptsObj->getSqlAssoc($resgetPaySum);
				
				$qryInsPaySum= "Insert into tblPayrollSummaryHist
										(compCode,pdYear,pdNumber,empNo,payGrp,payCat,empLocCode,empBrnCode,
										  empBnkCd,grossEarnings,taxableEarnings,totDeductions,nonTaxAllow,
										netSalary,taxWitheld,empDivCode,empDepCode,empSecCode,sprtAllow,sprtAllowAdvance,empBasic,empMinWageTag,empEcola,
										emp13thMonthNonTax,emp13thMonthTax,emp13thAdvances,empTeu,empYtdTaxable,empYtdTax,empYtdGovDed)
										values('".$arr_empInfo["compCode"]."','".$arrgetpaySum["pdYear"]."','".$arrgetpaySum["pdNumber"]."',
										'".$arr_empInfo['empNo']."','".$arr_empInfo["empPayGrp"]."','".$arr_empInfo["empPayCat"]."','".$arr_empInfo["empBrnCode"]."','".$arr_empInfo["empBrnCode"]."',
										'".$arr_empInfo["empBankCd"]."','".$arrgetpaySum["grossEarnings"]."','".$arrgetpaySum["taxableEarnings"]."','".$arrgetpaySum["totDeductions"]."','".$arrgetpaySum["nonTaxAllow"]."',
										'".$arrgetpaySum["netSalary"]."','".$arrgetpaySum["taxwitheld"]."','".$arr_empInfo["empDiv"]."','".$arr_empInfo["empDepCode"]."',
										'".$arr_empInfo["empSecCode"]."','".$arrgetpaySum["sprtAllow"]."','0',
										'".$arrgetpaySum["empBasic"]."','".$arr_empInfo["empWageTag"]."','".$arrgetpaySum["EmpEcola"]."','0','0','0','".$arr_empInfo["empTeu"]."',
										'".$arrgetpaySum["taxableEarnings"]."', '".$arrgetpaySum["taxwitheld"]."','0');<br>";
				
				$qrygetYtdData = "SELECT     *
									FROM         fontillasYtdData$ where empNo='790000001'
									";
				
				$resgetYtdData = $scriptsObj->execQry($qrygetYtdData);
				$arrgetYtdData = $scriptsObj->getSqlAssoc($resgetYtdData);
				
				$qryInsPaySum.= "Insert into tblYtdDataHist(compCode,pdYear,empNo,YtdGross,YtdTaxable,
											YtdGovDed,YtdTax,YtdNonTaxAllow,Ytd13NBonus,YtdTx13NBonus,payGrp,pdNumber,YtdBasic,sprtAllow)						
								values(
									  '".$arr_empInfo["compCode"]."',
									  '".$arrgetYtdData["pdYear"]."',
									  '".$arrgetYtdData["empNo"]."',
									  '".sprintf("%01.2f",$arrgetYtdData["ytdGross"])."',
									  '".sprintf("%01.2f",$arrgetYtdData["ytdTaxable"])."',
									  '".sprintf("%01.2f",$arrgetYtdData["ytdGovDed"])."',
									  '".sprintf("%01.2f",$arrgetYtdData["ytdTax"])."',
									  '0', '0', '0',
									  '".$arr_empInfo["empPayGrp"]."',
									  '17',
									  '".sprintf("%01.2f",$arrgetYtdData["ytdBasic"])."',
									  '".sprintf("%01.2f",$arrgetYtdData["sprtAllow"])."');";
									  				
				echo $qryInsPaySum;
				
				
				//$qry = "Select";
			break;
			
			case "MIGBIO":
				$qrygetBio_Data = "Select * from BIO_SP$";
				
				$resqrygetBio_Data = $scriptsObj->execQry($qrygetBio_Data);
				$arrqrygetBio_Data = $scriptsObj->getArrRes($resqrygetBio_Data);
				
				foreach($arrqrygetBio_Data as $arrqrygetBio_DataDtl)
				{
					$qryInsBio.= "Insert into tblBioEmp(compCode,locCode,bioNumber, empNo, bioStat) values ('".$_SESSION["company_code"]."', '49', '".$arrqrygetBio_DataDtl["Bio - Number"]."','".$arrqrygetBio_DataDtl["Employee No"]."','A');<br>";
				}
				
				echo $qryInsBio;
			break;
			
			case "MIGEMP":
				$arrEmp = array('200001838',
								'320000242',
								'320000404',
								'140001638',
								'140000090',
								'040000080'
								);
				
				$arrHeader = array(
									'PAYDATE',	
									'EMPLOYEE ID#',	
									'TAX STATUS',	
									'BRANCH CODE',	
									'DEPARTMENT',	
									'SALARY',	
									'LEGAL PAY',	
									'DAILY RATE',	
									'ABSENCES AMOUNT',
									'TARDY AMOUNT',	
									'UNDERTIME AMOUNT',	
									'OVERTIME AMOUNT',	
									'ND AMOUNT',	
									'ADJUSMENTS',
									'GROSS INCOME',	
									'WITHOLDING TAX AND TAX ADJUSMENT',	
									'EMPLOYEE SSS',	
									'EMPLOYEE MCR',	
									'EMPLOYEE PAG-IBIG',	
									'DEDUCTIONS',	
									'NET SALARY',	
									'EMPLOYER SSS',	
									'EMPLOYER MCR',	
									'EMPLOYER EC',	
									'EMPLOYER PAG-IBIG',
									'EMP. BASIC');
				
										
				$arr_mtdMonthNo = array(2=>1,4=>2,6=>3,8=>4,10=>5,12=>6,14=>7,16=>8,18=>9);
				
				
				echo "<table border='1' width='100%' style='border-collapse:collapse'>";		
					echo "<tr>";
						foreach($arrHeader as $arrHeader_val)
						{
							echo "<td>".$arrHeader_val."</td>";
						}
					echo "</tr>";		
					
					foreach ($arrEmp as $arrEmp_val)
					{
						//Get EmpMast Record
						$arr_EmpInfo = $scriptsObj -> getTblData('tblEmpMast', " and empNo='".$arrEmp_val."'", '', 'sqlAssoc');
						$arr_PaySum = 	$scriptsObj -> getTblData('tblPayrollSummaryHist', " and empNo='".$arrEmp_val."' and pdYear='2010' ", ' order by pdNumber', '');
						
						foreach($arr_PaySum as $arr_PaySum_val)
						{
							$arr_PayOutDate = $scriptsObj -> getTblData('tblPayPeriod', " and payGrp='".$arr_PaySum_val["payGrp"]."' and payCat='".$arr_PaySum_val["payCat"]."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdNumber='".$arr_PaySum_val["pdNumber"]."'", '', 'sqlAssoc');
							$arr_Dept_Desc = $scriptsObj ->getDeptDescGen($_SESSION["company_code"],$arr_PaySum_val["empDivCode"],$arr_PaySum_val["empDepCode"]);
							$arr_Earnings_Sal = $scriptsObj -> getTblData('tblEarningsHist', " and empNo='".$arrEmp_val."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdNumber='".$arr_PaySum_val["pdNumber"]."' and trnCode='".EARNINGS_BASIC."'", '', 'sqlAssoc');
							$arr_Earnings_LegalPay = $scriptsObj -> getTblData('tblEarningsHist', " and empNo='".$arrEmp_val."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdNumber='".$arr_PaySum_val["pdNumber"]."' and trnCode='".EARNINGS_LEGALPAY."'", '', 'sqlAssoc');
							
							$arr_Earnings_Absences = $scriptsObj -> getTblData('tblEarningsHist', " and empNo='".$arrEmp_val."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdNumber='".$arr_PaySum_val["pdNumber"]."' and trnCode='".EARNINGS_ABS."'", '', 'sqlAssoc');
							$arr_Earnings_Tardy = $scriptsObj -> getTblData('tblEarningsHist', " and empNo='".$arrEmp_val."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdNumber='".$arr_PaySum_val["pdNumber"]."' and trnCode='".EARNINGS_TARD."'", '', 'sqlAssoc');
							$arr_Earnings_Ut = $scriptsObj -> getTblData('tblEarningsHist', " and empNo='".$arrEmp_val."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdNumber='".$arr_PaySum_val["pdNumber"]."' and trnCode='".EARNINGS_UT."'", '', 'sqlAssoc');
							$arr_Earnings_OT = $scriptsObj ->  getDatatblEarningsOTND(EARNINGS_OT,$arr_PaySum_val["pdYear"],$arr_PaySum_val["pdNumber"], $arrEmp_val );
							$arr_Earnings_Nd = $scriptsObj ->  getDatatblEarningsOTND(EARNINGS_ND,$arr_PaySum_val["pdYear"],$arr_PaySum_val["pdNumber"], $arrEmp_val );
							$arr_EarnAdjustments = $scriptsObj ->getDataOthtblEarnings($arr_PaySum_val["pdYear"],$arr_PaySum_val["pdNumber"], $arrEmp_val);
							$arr_TaxAndAdj = $scriptsObj ->getWTaxAndTaxAdj($arr_PaySum_val["pdYear"],$arr_PaySum_val["pdNumber"], $arrEmp_val);
							$arr_DedLoans = $scriptsObj ->getDataLoanstblDeductions($arr_PaySum_val["pdYear"],$arr_PaySum_val["pdNumber"], $arrEmp_val);
							$arr_OtherDed = $scriptsObj ->getDataOthAdjtblDeductions($arr_PaySum_val["pdYear"],$arr_PaySum_val["pdNumber"], $arrEmp_val);
							
							echo "<tr>";
								echo "<td>".date("m/d/Y", strtotime($arr_PayOutDate["pdPayable"]))."</td>";
								echo "<td>".$arr_PaySum_val["empNo"]."</td>";
								echo "<td>".$arr_PaySum_val["empBrnCode"]."</td>";
								echo "<td>".$arr_PaySum_val["empBrnCode"]."</td>";
								echo "<td>".$arr_Dept_Desc["deptDesc"]."</td>";
								echo "<td>".$arr_Earnings_Sal["trnAmountE"]."</td>";
								echo "<td>".$arr_Earnings_LegalPay["trnAmountE"]."</td>";
								echo "<td>".$arr_EmpInfo["empDrate"]."</td>";
								echo "<td>".$arr_Earnings_Absences["trnAmountE"]."</td>";
								echo "<td>".$arr_Earnings_Tardy["trnAmountE"]."</td>";
								echo "<td>".$arr_Earnings_Ut["trnAmountE"]."</td>";
								echo "<td>".$arr_Earnings_OT["totAmountE"]."</td>";
								echo "<td>".$arr_Earnings_Nd["totAmountE"]."</td>";
								echo "<td>".$arr_EarnAdjustments["totAmountE"]."</td>";
								echo "<td>".$arr_PaySum_val["grossEarnings"]."</td>";
								echo "<td>".$arr_TaxAndAdj["totTax"]."</td>";
								
								if(($arr_PaySum_val["pdNumber"]%2)=='0')
								{
									$arr_getMtdGovtHist =  $scriptsObj -> getTblData('tblMtdGovtHist', " and empNo='".$arrEmp_val."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdMonth='".$arr_mtdMonthNo[$arr_PaySum_val["pdNumber"]]."'", '', 'sqlAssoc');
							
									echo "<td>".$arr_getMtdGovtHist["sssEmp"]."</td>";
									echo "<td>".$arr_getMtdGovtHist["phicEmp"]."</td>";
									echo "<td>".$arr_getMtdGovtHist["hdmfEmp"]."</td>";
								}
								else
								{
									echo "<td></td>";
									echo "<td></td>";
									echo "<td></td>";
								}
								
								echo "<td>".($arr_DedLoans["totAmountD"]+$arr_OtherDed["totAmountD"])."</td>";
								echo "<td>".$arr_PaySum_val["netSalary"]."</td>";
								
								if(($arr_PaySum_val["pdNumber"]%2)=='0')
								{
									$arr_getMtdGovtHist =  $scriptsObj -> getTblData('tblMtdGovtHist', " and empNo='".$arrEmp_val."' and pdYear='".$arr_PaySum_val["pdYear"]."' and pdMonth='".$arr_mtdMonthNo[$arr_PaySum_val["pdNumber"]]."'", '', 'sqlAssoc');
							
									echo "<td>".$arr_getMtdGovtHist["sssEmplr"]."</td>";
									echo "<td>".$arr_getMtdGovtHist["phicEmplr"]."</td>";
									echo "<td>".$arr_getMtdGovtHist["ec"]."</td>";
									echo "<td>".$arr_getMtdGovtHist["hdmfEmp"]."</td>";
								}
								else
								{
									echo "<td></td>";
									echo "<td></td>";
									echo "<td></td>";
									echo "<td></td>";
								}
								
								echo "<td>".$arr_PaySum_val["empBasic"]."</td>";
							echo "</tr>";
						}
						
						
					}
				echo "</table>";
			break;
			
			case "SPERTBL":
				/*$arr_201_module = array('tbl201Menu',
										'tblBranchGrp',
										'tblCitizenshipRef',
										'tblCityRef',
										'tblCompany',
										'tblCompType',
										'tblContactTypeRef',
										'tblDeptTrantbl',
										'tblEmpLevel',
										'tblPayBank',
										'tblPayCat',
										'tblProcGrp',
										'tblRankType',
										'tblReligionRef');*/
				
				/*$arr_201_module = array('tblAllowance',
											'tblBioEmp',
											'tblBlacklisted_Paradox',
											'tblBlacklistedEmp',
											'tblBranch',
											'tblContactMast',
											'tblDepartment',
											'tblEmpMast',
											'tblEmpMast_new',
											'tblEmpNo',
											'tblEmpRestDayBckUp',
											'tblHolidayCalendar',
											'tblPAF_Allowance',
											'tblPAF_Allowancehist',
											'tblPAF_Branch',
											'tblPAF_Branchhist',
											'tblPAF_CompTrans',
											'tblPAF_CompTranshist',
											'tblPAF_EmpStatus',
											'tblPAF_EmpStatushist',
											'tblPAF_Others',
											'tblPAF_Othershist',
											'tblPAF_PayrollRelated',
											'tblPAF_PayrollRelatedhist',
											'tblPAF_Position',
											'tblPAF_Positionhist',
											'tblPAF_RefNo',
											'tblPosition',
											'tblPrevEmployer'
											'tblEmpRestDayBckUp',
											'tblPAF_Allowancehist',
											'tblPAF_Branchhist',
											'tblPAF_CompTranshist',
											'tblPAF_EmpStatushist',
											'tblPAF_Othershist',
											'tblPAF_PayrollRelatedhist',
											'tblPAF_Positionhist',);		*/	
				
				/*$arr_201_module = array('tblEmpRestDayBckUp',
											'tblPAF_Allowancehist',
											'tblPAF_Branchhist',
											'tblPAF_CompTranshist',
											'tblPAF_EmpStatushist',
											'tblPAF_Othershist',
											'tblPAF_PayrollRelatedhist',
											'tblPAF_Positionhist');		*/
											
					/*$arr_201_module = array('tblAllowType',
											'tblAllowTypeConvTbl',
											'tblAnnTax',
											'tblDayType',
											'tblDedRefNo',
											'tblDenomList',
											'tblGLCodes',
											'tblGLMajorAcct',
											'tblGLMinorAcct',
											'tblGLPayrollAcct',
											'tblGLStoreAcct',
											'tblGovAgencies',
											'tblLoanType',
											'tblOtPrem',
											'tblPayJournal',
											'tblPayPeriod',
											'tblPayReason',
											'tblPayrollMenu',
											'tblPayTransType',
											'tblSmTax',
											'tblSssPhic',
											'tblTeu',
											'tblUserDefinedRef',
											'tblUserDefLookUp');	*/	
											
											
		/*$arr_201_module = array('tblAllowanceBrkDwnHst',
								'tblCustomerNo',
								'tblDedTranDtlHist',
								'tblDedTranHeader',
								'tblDeductionsHist',
								'tblDeductionsMinor',
								'tblEarningsHist',
								'tblEarnRefNo',
								'tblEarnTranDtlHist',
								'tblEarnTranHeader',
								'tblEmpID',
								'tblEmpLoans',
								'tblEmpLoansDtlHist',
								'tblEmpTax',
								'tblGovPayments',
								'tblLastPaybal',
								'tblLastPayData',
								'tblLastPayEmp',
								'tblLoansAdj',
								'tblMtdGovtHist',
								'tblPayrollSummaryHist',
								'tblTimeSheetHist',
								'tblTimeShiftRef',
								'tblTsParadox',
								'tblUserDefinedMst',
								'tblUserBranch',
								'tblUsers',
								'tblUnpostedTranHist',
								'tblYtdDataHist',
								'wGovJm',
								'wGovJmS',
								'wPayJournal1',
								'wPayJournal2',
								'wPayJournal2d',
								'wPayJournal3',
								'wPayJournal3d');*/
								
				/*$arr_201_module = array('tblTK_AppTypes',
											'tblTK_RankLevelTimeExempt',
											'tblTK_RefNo',
											'tblTK_ShiftDtl',
											'tblTK_ShiftHdr',
											'tblTK_ViolationType',
											'tblTmeInAttendanceMenu');
										*/
										
				/*$arr_201_module = array('tblTK_ChangeRDAppHist',
										'tblTK_CSAppHist',
										'tblTK_DeductionsHist',
										'tblTK_EmpShiftHist',
										'tblTK_EventLogs',
										'tblTK_LeaveAppHist',
										'tblTK_OBAppHist',
										'tblTK_OTAppHist',
										'tblTK_OvertimeHist',
										'tblTK_ScheduleHist',
										'tblTK_TimeSheetCorr_original',
										'tblTK_TimeSheetCorrHist',
										'tblTK_TimeSheetCorrLogs',
										'tblTK_TimesheetHist',
										'tblTK_UserBranch',
										'tblTK_UTAppHist',
										'tblTsCorr');*/
										
						$arr_201_module =		array('tblTK_ChangeRDApp',
										'tblTK_CSApp',
										'tblTK_Deductions',
										'tblTK_EmpShift',
										'tblTK_LeaveApp',
										'tblTK_OBApp',
										'tblTK_OTApp',
										'tblTK_Overtime',
										'tblTK_Timesheet',
										'tblTK_TimeSheetCorr',
										'tblTK_UTApp');

				echo "<table border='1' width='100%'>";				
				$ctr=1;		
				$sumData = 0;
				foreach ($arr_201_module as $arr_201_module_val)
				{
					echo "<tr>";
						echo "<td>".$ctr.". ".$arr_201_module_val."</td>";
						
						$qryExecSpace = "exec sp_spaceused ".$arr_201_module_val."";
						$resqryExecSpace = $scriptsObj->execQry($qryExecSpace);
						$arrqryExecSpace = $scriptsObj->getSqlAssoc($resqryExecSpace);
						
						echo "<td>".$arrqryExecSpace["data"]."</td>";
						$sumData+=$arrqryExecSpace["data"];
					echo "</tr>";
					$ctr++;
				}
				echo $sumData;
				echo "</table>";
			break;
			
			case "AGELIST":
				$arrayHeader = array('1'=>'5 years of service below',
								'2'=>'5 years of service above but less than 10',
								'3'=>'10 years above but less than 15',
								'4'=>'15 years above but less than 20',
								'5'=>'20 years above'); 

				$arrAgeHeader = array('20'=>'20 & below',
								'21'=>'21-25',
								'26'=>'26-30',
								'31'=>'31-35',
								'36'=>'36-40',
								'41'=>'41-45',
								'46'=>'46-50',
								'51'=>'51-55',
								'56'=>'56-60',
								'61'=>'61-65',
								'66'=>'66 & above');

				echo "<table border='1'  width='100%' style='border-collapse:collapse'>";
					$qryEmp = "Select * from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empStat in ('RG') and empPayGrp in (1,2)
								and empNo not in (Select empNo from tblLastPayEmp) order by empLastName;";
					
					$resqryEmp = $scriptsObj->execQry($qryEmp);
					$arrqryEmp = $scriptsObj->getArrRes($resqryEmp);
			
					echo "<tr>";
							echo "<td></td>";
							echo "<td>Head Count</td>";
							foreach($arrAgeHeader as $arrAgeHeader_val=>$indexAgeHeaderVal)
							{
								echo "<td>".$indexAgeHeaderVal."</td>";
							}
					echo "</tr>";
					
					foreach($arrayHeader as $arrayHeader_val=>$indexHeaderVal)
					{
						echo "<tr style='height:40px;'>";
							echo "<td>".$indexHeaderVal."</td>";
							
							$years  =  0;
							$ctr_Ab15yrs_bday20 = $ctr_Ab15yrs_bday21 = $ctr_Ab15yrs_bday26 = $ctr_Ab15yrs_bday31 = $ctr_Ab15yrs_bday36 = $ctr_Ab15yrs_bday41 = $ctr_Ab15yrs_bday46= $ctr_Ab15yrs_bday51 = $ctr_Ab15yrs_bday56 = $ctr_Ab15yrs_bday61 = $ctr_Ab15yrs_bday66 = 0;
							
							foreach($arrqryEmp as $arrqryEmp_val)
							{
								$date1 = date("Y-m-d", strtotime($arrqryEmp_val["dateHired"]));
								$date2 = "2010-10-12";
								
								$diff = abs(strtotime($date2) - strtotime($date1));
								$years = floor($diff / (365*60*60*24));
								
								$dateb_day1 = date("Y-m-d", strtotime($arrqryEmp_val["empBday"]));
								$dateb_day2 = "2010-10-12";
								
								$diffb_day = abs(strtotime($dateb_day2) - strtotime($dateb_day1));
								$yearsb_day = floor($diffb_day / (365*60*60*24));
								
								if(($arrayHeader_val=='1') and ($years<5))
								{
									$ctryrs[1]++;
									if($yearsb_day<=20)
										$ctrbdayYears[201]++;
									elseif(($yearsb_day>=21) and ($yearsb_day<=25))
										$ctrbdayYears[211]++;
									elseif(($yearsb_day>=26) and ($yearsb_day<=30))
										$ctrbdayYears[261]++;
									elseif(($yearsb_day>=31) and ($yearsb_day<=35))
										$ctrbdayYears[311]++;
									elseif(($yearsb_day>=36) and ($yearsb_day<=40))
										$ctrbdayYears[361]++;
									elseif(($yearsb_day>=41) and ($yearsb_day<=45))
										$ctrbdayYears[411]++;
									elseif(($yearsb_day>=46) and ($yearsb_day<=50))
										$ctrbdayYears[461]++;
									elseif(($yearsb_day>=51) and ($yearsb_day<=55))
										$ctrbdayYears[511]++;
									elseif(($yearsb_day>=56) and ($yearsb_day<=60))
										$ctrbdayYears[561]++;
									elseif(($yearsb_day>=61) and ($yearsb_day<=65))
										$ctrbdayYears[611]++;
									elseif($yearsb_day>=66)
										$ctrbdayYears[661]++;
								}
								elseif(($arrayHeader_val=='2') and (($years>=5) and ($years<10)))
								{
									$ctryrs[2]++;
									if($yearsb_day<=20)
										$ctrbdayYears[202]++;
									elseif(($yearsb_day>=21) and ($yearsb_day<=25))
										$ctrbdayYears[212]++;
									elseif(($yearsb_day>=26) and ($yearsb_day<=30))
										$ctrbdayYears[262]++;
									elseif(($yearsb_day>=31) and ($yearsb_day<=35))
										$ctrbdayYears[312]++;
									elseif(($yearsb_day>=36) and ($yearsb_day<=40))
										$ctrbdayYears[362]++;
									elseif(($yearsb_day>=41) and ($yearsb_day<=45))
										$ctrbdayYears[412]++;
									elseif(($yearsb_day>=46) and ($yearsb_day<=50))
										$ctrbdayYears[462]++;
									elseif(($yearsb_day>=51) and ($yearsb_day<=55))
										$ctrbdayYears[512]++;
									elseif(($yearsb_day>=56) and ($yearsb_day<=60))
										$ctrbdayYears[562]++;
									elseif(($yearsb_day>=61) and ($yearsb_day<=65))
										$ctrbdayYears[612]++;
									elseif($yearsb_day>=66)
										$ctrbdayYears[662]++;
								}
								elseif(($arrayHeader_val=='3') and (($years>=10) and ($years<15)))
								{
									$ctryrs[3]++;
									if($yearsb_day<=20)
										$ctrbdayYears[203]++;
									elseif(($yearsb_day>=21) and ($yearsb_day<=25))
										$ctrbdayYears[213]++;
									elseif(($yearsb_day>=26) and ($yearsb_day<=30))
										$ctrbdayYears[263]++;
									elseif(($yearsb_day>=31) and ($yearsb_day<=35))
										$ctrbdayYears[313]++;
									elseif(($yearsb_day>=36) and ($yearsb_day<=40))
										$ctrbdayYears[363]++;
									elseif(($yearsb_day>=41) and ($yearsb_day<=45))
										$ctrbdayYears[413]++;
									elseif(($yearsb_day>=46) and ($yearsb_day<=50))
										$ctrbdayYears[463]++;
									elseif(($yearsb_day>=51) and ($yearsb_day<=55))
										$ctrbdayYears[513]++;
									elseif(($yearsb_day>=56) and ($yearsb_day<=60))
										$ctrbdayYears[563]++;
									elseif(($yearsb_day>=61) and ($yearsb_day<=65))
										$ctrbdayYears[613]++;
									elseif($yearsb_day>=66)
										$ctrbdayYears[663]++;
								}
								elseif(($arrayHeader_val=='4') and (($years>=15) and ($years<20)))
								{
									$ctryrs[4]++;
									if($yearsb_day<=20)
										$ctrbdayYears[204]++;
									elseif(($yearsb_day>=21) and ($yearsb_day<=25))
										$ctrbdayYears[214]++;
									elseif(($yearsb_day>=26) and ($yearsb_day<=30))
										$ctrbdayYears[264]++;
									elseif(($yearsb_day>=31) and ($yearsb_day<=35))
										$ctrbdayYears[314]++;
									elseif(($yearsb_day>=36) and ($yearsb_day<=40))
										$ctrbdayYears[364]++;
									elseif(($yearsb_day>=41) and ($yearsb_day<=45))
										$ctrbdayYears[414]++;
									elseif(($yearsb_day>=46) and ($yearsb_day<=50))
										$ctrbdayYears[464]++;
									elseif(($yearsb_day>=51) and ($yearsb_day<=55))
										$ctrbdayYears[514]++;
									elseif(($yearsb_day>=56) and ($yearsb_day<=60))
										$ctrbdayYears[564]++;
									elseif(($yearsb_day>=61) and ($yearsb_day<=65))
										$ctrbdayYears[614]++;
									elseif($yearsb_day>=66)
										$ctrbdayYears[664]++;
								}
								elseif(($arrayHeader_val=='5') and ($years>20))
								{
									$ctryrs[5]++;
									if($yearsb_day<=20)
										$ctrbdayYears[205]++;
									elseif(($yearsb_day>=21) and ($yearsb_day<=25))
										$ctrbdayYears[215]++;
									elseif(($yearsb_day>=26) and ($yearsb_day<=30))
										$ctrbdayYears[265]++;
									elseif(($yearsb_day>=31) and ($yearsb_day<=35))
										$ctrbdayYears[315]++;
									elseif(($yearsb_day>=36) and ($yearsb_day<=40))
										$ctrbdayYears[365]++;
									elseif(($yearsb_day>=41) and ($yearsb_day<=45))
										$ctrbdayYears[415]++;
									elseif(($yearsb_day>=46) and ($yearsb_day<=50))
										$ctrbdayYears[465]++;
									elseif(($yearsb_day>=51) and ($yearsb_day<=55))
										$ctrbdayYears[515]++;
									elseif(($yearsb_day>=56) and ($yearsb_day<=60))
										$ctrbdayYears[565]++;
									elseif(($yearsb_day>=61) and ($yearsb_day<=65))
										$ctrbdayYears[615]++;
									elseif($yearsb_day>=66)
										$ctrbdayYears[665]++;
								}
							}
							
							
								echo "<td>".$ctryrs[$arrayHeader_val]."</td>";
								foreach($arrAgeHeader as $arrAgeHeader_val=>$indexAgeHeaderVal)
								{
									echo "<td>".$ctrbdayYears[$arrAgeHeader_val.$arrayHeader_val]."</td>";
								}
								
						echo "</tr>";
						
					}	
					
				echo "</table>";
			
			break;
			
			case '13TH':
				$grand_divTotals = $grand_divTotals2 = 0;
				$qryDept = "Select paySum.empBrnCode, brnch.brnDesc, paySum.empDivCode,div.deptDesc as divDesc, 
							paySum.empdepCode, sum(netSalary) as estimated13Th 
							from 
							tblPayrollSummary paySum, 
							tblDepartment div, tblBranch brnch
							
							where 
							pdNumber='25' and pdYear='2010' and 
							paySum.compCode='2' and div.compCode='2' and 
							empDivCode=div.divCode and div.deptLevel='1' and brnch.compCode='2' and empBrnCode=brnCode  
							group by paySum.empBrnCode,brnch.brnDesc, empDivCode, div.deptDesc,empDepCode
							order by brnch.brnDesc,div.deptDesc, empdepCode";
				$resqryDept = $scriptsObj->execQry($qryDept);
				$arrqryDept = $scriptsObj->getArrRes($resqryDept);
				
				echo "<table border='1'  width='100%' style='border-collapse:collapse'>";
					echo "<tr align='center' style='height:30px;'>";
						echo "<td width='45%'>Description</td>";
						echo "<td>13th Month (as of Oct. 31)</td>";
						echo "<td>13th Month  (as of Nov. 30)</td>";
					echo "</tr>";
					
					foreach($arrqryDept as $arrqryDept_val)
					{
						$deptDesc = $scriptsObj->getDeptDescGen($_SESSION["company_code"],$arrqryDept_val["empDivCode"],$arrqryDept_val["empdepCode"]);
						
						if($temp_brn!=$arrqryDept_val["brnDesc"])
						{
							/*echo "<tr style='height:30px;'>";
								echo "<td align='left'>GRAND TOTAL</td>";
								echo "<td align='left'>GRAND TOTAL</td>";
								echo "<td align='left'>GRAND TOTAL</td>";
							echo "</tr>";
							*/
							echo "<tr style='height:30px;'>";
								echo "<td align='left' colspan='3'>".$arrqryDept_val["brnDesc"]."</td>";
							echo "</tr>";
							
							
						}
						
						if($temp_div!=$arrqryDept_val["divDesc"])
						{
							$divTotals = $divTotals2 = 0;
							echo "<tr style='height:30px;'>";
								echo "<td align='left' colspan='3'>".$arrqryDept_val["divDesc"]."</td>";
							echo "</tr>";
							
							
								$qryDept_test = "Select empDepCode, deptDesc, sum(netSalary) as deptestimated13Th
												from tblPayrollSummary tblPaySum, tblDepartment tblDept
												where tblPaySum.compCode='2' and tblDept.compCode='2' and
												empBrnCode='".$arrqryDept_val["empBrnCode"]."' and empDivCode='".$arrqryDept_val["empDivCode"]."' and
												empDepCode=deptCode and deptLevel='2' and divCode='".$arrqryDept_val["empDivCode"]."'
												group by empDepCode, deptDesc
												order by deptDesc";
								$resqryDept_test = $scriptsObj->execQry($qryDept_test);
								$arrqryDept_test = $scriptsObj->getArrRes($resqryDept_test);
								
								foreach($arrqryDept_test as $arrqryDept_test_val)
								{
									echo "<tr style='height:30px;'>";
										echo "<td align='right'>".$arrqryDept_test_val["deptDesc"]."</td>";
										echo "<td align='right'>".number_format($arrqryDept_test_val["deptestimated13Th"],2)."</td>";
										echo "<td align='right'>".number_format((($arrqryDept_test_val["deptestimated13Th"]*12)/11),2)."</td>";
										$divTotals+=$arrqryDept_test_val["deptestimated13Th"];
										$divTotals2+=(($arrqryDept_test_val["deptestimated13Th"]*12)/11);
										
										$grand_divTotals+=$arrqryDept_test_val["deptestimated13Th"];
										$grand_divTotals2+=(($arrqryDept_test_val["deptestimated13Th"]*12)/11);
									echo "</tr>";	
								}
								
							echo "<tr style='height:30px;'>";
								echo "<td align='right'>DIVISION TOTAL</td>";
								echo "<td align='right' >".number_format($divTotals,2)."</td>";
								echo "<td align='right' >".number_format($divTotals2,2)."</td>";
							echo "</tr>";
								
						}
						
						
						
						$temp_brn = $arrqryDept_val["brnDesc"];
						$temp_div = $arrqryDept_val["divDesc"];
						$temp_deptcd = $arrqryDept_val["empdepCode"];
					}
						echo "<tr style='height:30px;'>";
							echo "<td align='right'>GRAND TOTAL</td>";
							echo "<td align='right' >".number_format($grand_divTotals,2)."</td>";
							echo "<td align='right' >".number_format($grand_divTotals2,2)."</td>";
						echo "</tr>";
				echo "</table>";
				/*$qryDept = "Select paySum.empBrnCode,brnch.brnDesc, sum(netSalary) as estimated13Th 
							from tblPayrollSummary paySum, tblBranch brnch
							where 
							pdNumber='25' and pdYear='2010' and paySum.compCode='2' and brnch.compCode='2' and empBrnCode=brnCode
							group by paySum.empBrnCode,brnch.brnDesc
							order by brnch.brnDesc
							";*/
				/*$resqryDept = $scriptsObj->execQry($qryDept);
				$arrqryDept = $scriptsObj->getArrRes($resqryDept);
				
				
			
				echo "<table border='1'  width='100%' style='border-collapse:collapse'>";
					echo "<tr align='center' style='height:30px;'>";
						echo "<td>Branch Description</td>";
						echo "<td>13th Month (as of Oct. 31)</td>";
						echo "<td>13th Month  (as of Nov. 30)</td>";
					echo "</tr>";
					
					foreach($arrqryDept as $arrqryDept_val)
					{
						echo "<tr  style='height:30px;'>";
							echo "<td align='left'>".$arrqryDept_val["brnDesc"]."</td>";
							echo "<td align='right'>".number_format($arrqryDept_val["estimated13Th"],2)."</td>";
						echo "</tr>";
					}
				echo "</table>";*/
			break;
			
			case "2316":
			
			$qry_lastPayEmp = "Select lstPayEmp.pdYear, lstPayEmp.pdNumber, lstPayEmp.payGrp, lstPayEmp.empNo, empLastName, empFirstName, empMidName, 
								empMast.empWageTag, empTin, dateResigned, ytdGross, ytd13NBonus, ytdGovDed, (ytdTaxable-ytdGovDed) as taxableSalary, 
								ytdtx13NBonus, empTeu, teuAmt, ytdTax, brnDesc, minWage from
								tblLastPayEmp lstPayEmp,
								tblEmpMast empMast,
								tblYtdDataHist ytdHist,
								tblTeu teu,
								tblBranch brnch
								where lstPayEmp.compCode='2' and empMast.compCode='2' and ytdHist.compCode='2'  and brnch.compCode='2' and
								lstPayEmp.empNo=empMast.empNo and lstPayEmp.empNo=ytdHist.empNo and empTeu=teuCode and empWageTag='Y' and empBrnCode=brnCode
								order by empLastName";
			$resqry_lastPayEmp = $scriptsObj->execQry($qry_lastPayEmp);
			$arrresqry_lastPayEmp = $scriptsObj->getArrRes($resqry_lastPayEmp);
			echo "<table border='1' width='100%' style='border-collapse:collapse'>";
			echo "<tr style='height:35px;'>";
				echo "<td colspan='4'></td>";
				echo "<td colspan='2'>EMPLOYMENT</td>";
				echo "<td colspan='11'></td>";
			echo "</tr>";
			
			echo "<tr style='height:35px;'>";
				echo "<td>TIN. NO.</td>";
				echo "<td>LAST<br>NAME</td>";
				echo "<td>FIRST<br>NAME</td>";
				echo "<td>MIDDLE<br>NAME</td>";
				echo "<td>FROM</td>";
				echo "<td>TO</td>";
				echo "<td>GROSS<br>INCOME</td>";
				echo "<td>13TH MONTH<br>NON TAXABLE</td>";
				echo "<td>GOVERNMENTALS</td>";
				echo "<td>TAXABLE<br>B.SAL.</td>";
				echo "<td>13TH MONTH<br>TAXABLE</td>";
				echo "<td>EXEMPT.<br>CODE</td>";
				echo "<td>AMOUNT<br>EXEMPT.</td>";
				//echo "<td>NET COMP<br>INC.</td>";
				//echo "<td>TAX DUE<br>(JAN-DEC)</td>";
				echo "<td>TAX DUE<br>(JAN-NOV)</td>";
				echo "<td>BRANCH</td>";
				echo "<td>MIN.WAGE</td>";
			echo "</tr>";
			
			foreach($arrresqry_lastPayEmp as $arrresqry_lastPayEmp_val)
			{
				echo "<tr>";
					echo "<td>".$arrresqry_lastPayEmp_val["empTin"]."</td>";
					echo "<td>".$arrresqry_lastPayEmp_val["empLastName"]."</td>";
					echo "<td>".$arrresqry_lastPayEmp_val["empFirstName"]."</td>";
					echo "<td>".$arrresqry_lastPayEmp_val["empMidName"]."</td>";
					echo "<td>01/01/2010</td>";
					echo "<td>".date("m/d/Y", strtotime($arrresqry_lastPayEmp_val["dateResigned"]))."</td>";
					echo "<td>".number_format($arrresqry_lastPayEmp_val["ytdGross"],2)."</td>";
					echo "<td>".number_format($arrresqry_lastPayEmp_val["ytd13NBonus"],2)."</td>";
					echo "<td>".number_format($arrresqry_lastPayEmp_val["ytdGovDed"],2)."</td>";
					echo "<td>".number_format($arrresqry_lastPayEmp_val["taxableSalary"],2)."</td>";
					echo "<td>".number_format($arrresqry_lastPayEmp_val["ytdtx13NBonus"],2)."</td>";
					echo "<td>".$arrresqry_lastPayEmp_val["empTeu"]."</td>";
					echo "<td>".$arrresqry_lastPayEmp_val["teuAmt"]."</td>";
					echo "<td>".number_format($arrresqry_lastPayEmp_val["ytdTax"],2)."</td>";
					echo "<td>".$arrresqry_lastPayEmp_val["brnDesc"]."</td>";
					echo "<td>".number_format($arrresqry_lastPayEmp_val["minWage"],2)."</td>";
				echo "</tr>";
			}
			
			echo "</table>";
			break;
			
			case "INSTBLEARN":
				//$qry_Earnings = "Select pdYear, pdNumber, empNo,ADJBASIC from JRPPCItblEarnings";
				$qry_Earnings = "Select * from tblEarningsHistUTTardy_JR";
				
				$resqry_Earningsp = $scriptsObj->execQry($qry_Earnings);
				$arrresresqry_Earningsp = $scriptsObj->getArrRes($resqry_Earningsp);
			
				foreach($arrresresqry_Earningsp  as $arrresresqry_Earningsp_val)
				{
					/*if($arrresresqry_Earningsp_val["advancesAmt"]!="")
					{
						$qryInsert.= "Insert into tblEarningsHist(compCode, pdYear, pdNumber, empNo, trnCode, trnAmountE, trnTaxCd, sprtPS)
									  values('2', '".$arrresresqry_Earningsp_val["pdYear"]."', '".$arrresresqry_Earningsp_val["pdNumber"]."',
												'".$arrresresqry_Earningsp_val["empNo"]."','".ALLW_ADVANCES."','".$arrresresqry_Earningsp_val["advancesAmt"]."',
												'Y', 'N');<br>";
					}*/
				
					if($arrresresqry_Earningsp_val["trnAmountE"]!="")
					{
						$qryInsert.= "Insert into tblEarningsHist(compCode, pdYear, pdNumber, empNo, trnCode, trnAmountE, trnTaxCd, sprtPS)
									  values('".$_SESSION["company_code"]."', '2011', '5',
												'".$arrresresqry_Earningsp_val["empNo"]."','".EARNINGS_TARD."','".$arrresresqry_Earningsp_val["trnAmountE"]."',
												'Y', 'N');<br>";
					}
					
					/*
					if($arrresresqry_Earningsp_val["LWOP"]!="")
					{
					$qryInsert.= "Insert into tblEarningsHist(compCode, pdYear, pdNumber, empNo, trnCode, trnAmountE, trnTaxCd, sprtPS)
								  values('2', '".$arrresresqry_Earningsp_val["pdYear"]."', '".$arrresresqry_Earningsp_val["pdNumber"]."',
								  			'".$arrresresqry_Earningsp_val["empNo"]."','".EARNINGS_ABS."','".($arrresresqry_Earningsp_val["LWOP"]*-1)."',
											'Y', 'N');<br>";
					}
					
					if($arrresresqry_Earningsp_val["TARDINESS"]!="")
					{
					$qryInsert.= "Insert into tblEarningsHist(compCode, pdYear, pdNumber, empNo, trnCode, trnAmountE, trnTaxCd, sprtPS)
								  values('2', '".$arrresresqry_Earningsp_val["pdYear"]."', '".$arrresresqry_Earningsp_val["pdNumber"]."',
								  			'".$arrresresqry_Earningsp_val["empNo"]."','".EARNINGS_TARD."','".($arrresresqry_Earningsp_val["TARDINESS"]*-1)."',
											'Y', 'N');<br>";
					}*/
					
					/*if($arrresresqry_Earningsp_val["UNDERTIME"]!="")
					{
					$qryInsert.= "Insert into tblEarningsHist(compCode, pdYear, pdNumber, empNo, trnCode, trnAmountE, trnTaxCd, sprtPS)
								  values('2', '".$arrresresqry_Earningsp_val["pdYear"]."', '".$arrresresqry_Earningsp_val["pdNumber"]."',
								  			'".$arrresresqry_Earningsp_val["empNo"]."','".EARNINGS_UT."','".($arrresresqry_Earningsp_val["UNDERTIME"]*-1)."',
											'Y', 'N');<br>";
					}*/
					
					/*if($arrresresqry_Earningsp_val["ADJBASIC"]!="")
					{
						$qryInsert.= "Insert into tblEarningsHist(compCode, pdYear, pdNumber, empNo, trnCode, trnAmountE, trnTaxCd, sprtPS)
									  values('2', '".$arrresresqry_Earningsp_val["pdYear"]."', '".$arrresresqry_Earningsp_val["pdNumber"]."',
												'".$arrresresqry_Earningsp_val["empNo"]."','".ADJ_BASIC."','".$arrresresqry_Earningsp_val["ADJBASIC"]."',
												'Y', 'N');<br>";
					}*/
				}
				
				echo $qryInsert;
			break;
			
			case "UPDATEPAYSUMADVANCE2009":
				$sql = "SELECT   sum(trnAmountE) as trnAmountE,empNo,pdNumber,pdYear FROM tblEarningshist  WHERE trnCode IN (8101,8119)
						 group by empNo,pdNumber,pdYear order by empNo, pdYear, pdNumber";
				$arrLoans = $scriptsObj->getArrRes($scriptsObj->execQry($sql));	
				foreach ($arrLoans as $val) 
				{
					$Amt = (float)$val['trnAmountE'];
					$qryupdateloans.="Update tblPayrollSummaryhist set sprtAllowAdvance=$Amt
										WHERE empNo='".$val['empNo']."' AND pdNumber='".$val['pdNumber']."' AND pdYear='".$val['pdYear']."'; <br>";
				}
				echo $qryupdateloans;
			break;
			
			case "UPDATEPAYSUMADVANCE":
				$sql = "SELECT   sum(trnAmountE) as trnAmountE,empNo,pdNumber,pdYear FROM tblEarningshist  WHERE trnCode IN (8101,8119) group by empNo,pdNumber,pdYear order by empNo, pdYear, pdNumber";
				$arrLoans = $scriptsObj->getArrRes($scriptsObj->execQry($sql));	
				foreach ($arrLoans as $val) 
				{
					$Amt = (float)$val['trnAmountE'];
					$qryupdateloans.="Update tblPayrollSummaryhist set sprtAllowAdvance=$Amt
										WHERE empNo='".$val['empNo']."' AND pdNumber='".$val['pdNumber']."' AND pdYear='".$val['pdYear']."'; <br>";
				}
				echo $qryupdateloans;
			break;
			
			case "UPDATEYTDDATAHISTADVANCE":
				$sql = "SELECT   empNo,sum(sprtAllowAdvance) as sprtAllowAdvance  FROM tblPayrollSummaryhist where pdYear=2010 and empNo in (010001392,
010001821,
010000938,
030000067,
010001722,
960000020,
490000011,
010000808,
210000002,
010000944,
010001381,
150000552,
010001782,
010001323,
610000005,
030000090,
230000001,
010001123,
010001129,
010001214,
010002312,
010001767,
360000140,
010001310,
660000032,
010001190,
010001334,
480000002,
010000989,
010001763,
630000023,
030000334,
010001863,
150000277,
150000005,
360000335,
010001666,
120000003,
190000011,
010001326,
010001283,
140000183,
040000079,
010000935,
310000005,
260000015,
270000005,
320000008,
010000933,
650000001,
490000017,
150000673,
010000955,
300000393,
360000012,
030000257,
040000033,
650000003,
030000650,
040001123,
030000114,
010002010,
010000797,
010001349,
120000011,
350000012,
010000379,
110000004,
560000003,
010001026,
150001176,
320000024,
480000030,
260000535,
570000001,
150000866)
 group by empNo";
				$arrLoans = $scriptsObj->getArrRes($scriptsObj->execQry($sql));	
				foreach ($arrLoans as $val) 
				{
					$Amt=(float)$val['trnAmountE'];
					$qryupdateloans.="Update tblYTDDatahist set sprtAdvance='".(float)$val['sprtAllowAdvance']."'
											WHERE empNo='".$val['empNo']."'; <br>";
				}
				echo $qryupdateloans;
			break;
			
			case "UPDATEYTDDATABASIC":
				$sql = "SELECT   *  FROM tblBasicReclass";
				$arrLoans = $scriptsObj->getArrRes($scriptsObj->execQry($sql));	
				foreach ($arrLoans as $val) 
				{
					$totBasicAdj=(float)$val['totBasicAdj'];
					$totRclsBasic=(float)$val['totRclsBasic'];
					$qryupdateloans.="Update tblYTDDatahist set basicReclass=$totBasicAdj-($totRclsBasic)
										WHERE empNo='".$val['empNo']."'; <br>";
				}
				echo $qryupdateloans;
			break;
			
			case "UPDATEYTDDATAALLOW":
				$sql = "SELECT   *  FROM tblAllowReclass";
				$arrLoans = $scriptsObj->getArrRes($scriptsObj->execQry($sql));	
				foreach ($arrLoans as $val) {
					$totRclsAdv=(float)$val['totRclsAdv'];
					$qryupdateloans.="Update tblYTDDatahist set allowReClass=$totRclsAdv
										WHERE empNo='".$val['empNo']."'; <br>";
				}
				
				echo $qryupdateloans;
			break;
			
			case "CONSOLPAYSUMEARNINGS";
				$sqlEmp = "Select * from tblEmpMast FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN')'";
				$resqry_sqlEmp = $scriptsObj->execQry($sqlEmp);
				$arrresqry_sqlEmp = $scriptsObj->getArrRes($resqry_sqlEmp);
				
				foreach($arrresqry_sqlEmp as $arrresqry_sqlEmp_val)
				{
					
				}
			break;
			
			case "TAXCHK":
				$qryEmployees = "Select * from tblYtdDataHist where compCode='".$_SESSION["company_code"]."' and pdYear='2010'";
				
			break;
			
			case "TRANSFER":
				//P_PAYMAS
				$qryp_paymas = "Select empNo, empBrnCode, empMrate, empDrate from tblEmpMast WHERE     (empNo IN (010001478, 030001579, 480000030, 120001216, 010002010, 130000913, 200001531, 140001295));";
				$resp_paymas = $scriptsObj->execQry($qryp_paymas);
				$arrrp_paymas = $scriptsObj->getArrRes($resp_paymas);
				
				echo "<table border='1' width='100%' style='border-collapse:collapse'>";
				echo "<tr style='height:35px;'>";
					echo "<td colspan='4'></td>";
					echo "<td colspan='2'>EMPLOYMENT</td>";
					echo "<td colspan='11'></td>";
				echo "</tr>";
				
				echo "<tr style='height:35px;'>";
					echo "<td>TIN. NO.</td>";
					echo "<td>LAST<br>NAME</td>";
					echo "<td>FIRST<br>NAME</td>";
					echo "<td>MIDDLE<br>NAME</td>";
					echo "<td>FROM</td>";
					echo "<td>TO</td>";
					echo "<td>GROSS<br>INCOME</td>";
					echo "<td>13TH MONTH<br>NON TAXABLE</td>";
					echo "<td>GOVERNMENTALS</td>";
					echo "<td>TAXABLE<br>B.SAL.</td>";
					echo "<td>13TH MONTH<br>TAXABLE</td>";
					echo "<td>EXEMPT.<br>CODE</td>";
					echo "<td>AMOUNT<br>EXEMPT.</td>";
					//echo "<td>NET COMP<br>INC.</td>";
					//echo "<td>TAX DUE<br>(JAN-DEC)</td>";
					echo "<td>TAX DUE<br>(JAN-NOV)</td>";
					echo "<td>BRANCH</td>";
					echo "<td>MIN.WAGE</td>";
				echo "</tr>";
				
				
				foreach($arrrp_paymas as $arrrp_paymasval)
				{
					
				}
				
				
			break;
			
			case "CHKMAMCINDY":
		
				/*echo "<table border='1' width='100%'  style='border-collapse:collapse'>";
					echo "<tr style='height:30px;' align='center'>";
						echo "<td width='20%'>Emp. No</td>";
						echo "<td width='10%'>PdNumber</td>";
						echo "<td width='20%'>Curr. taxEarnings.PaySum</td>";
						echo "<td width='20%'>Pdx. Curr. taxEarnings.PaySum</td>";
						echo "<td width='10%'>YTD Data HIST</td>";
						echo "<td width='10%'>To be Added</td>";
						echo "<td width='10%'>Final YTD</td>";
					echo "</tr>";
				
					$qryPaySum = "Select * from paySumParadox order by empNo,pdYear,pdNUmber";
					$rsPaySum = $scriptsObj->execQry($qryPaySum);
					$arrPaySum = $scriptsObj->getArrRes($rsPaySum);
					
					foreach($arrPaySum as $arrPaySum_val)
					{
						echo $qryUpdate = "Update tblPayrollSummaryHist set taxableEarnings='".$arrPaySum_val["taxableEarnings"]."' where empNo='".$arrPaySum_val["empNo"]."' and pdYear='".$arrPaySum_val["pdYear"]."' and pdNumber='".$arrPaySum_val["pdNUmber"]."'<br>";
						$tobeAdded  = $tobeAdded_Ytd = 0;
						$qryPaySumHist = "Select * from tblPayrollSummaryHist where empNo='".$arrPaySum_val["empNo"]."' and pdNumber='".$arrPaySum_val["pdNUmber"]."' and pdYear='".$arrPaySum_val["pdYear"]."'";
						$rsPaySumHist = $scriptsObj->execQry($qryPaySumHist);
						$arrPaySumHist = $scriptsObj->getSqlAssoc($rsPaySumHist);
						
						$qryalpha = "Select * from alphadtl where empNo='".$arrPaySum_val["empNo"]."'";
						$rsalpha = $scriptsObj->execQry($qryalpha);
						$arralpha = $scriptsObj->getSqlAssoc($rsalpha);
						
						$qryytdData = "Select * from tblYtdDataHist where empNo='".$arrPaySum_val["empNo"]."' and pdYear='".$arrPaySum_val["pdYear"]."'";
						$rsytdData = $scriptsObj->execQry($qryytdData);
						$arrytdData = $scriptsObj->getSqlAssoc($rsytdData);
						
						$tobeAdded = $arrPaySum_val["taxableEarnings"] - $arrPaySumHist["taxableEarnings"];
						$tobeAdded_Ytd = $tobeAdded + $arrytdData["YtdTaxable"];
						
						echo "<tr style='height:30px;' align='center'>";
							
							echo "<td width='20%'>".$arrPaySum_val["empNo"]."</td>";
							echo "<td width='10%'>".$arrPaySum_val["pdNUmber"]."</td>";
							echo "<td width='20%' align='right'>".$arrPaySumHist["taxableEarnings"]."</td>";
							echo "<td width='20%' align='right'>".$arrPaySum_val["taxableEarnings"]."</td>";
							echo "<td width='10%' align='right'>".$arrytdData["YtdTaxable"]."</td>";
							echo "<td width='10%' align='right'>".$tobeAdded."</td>";
							echo "<td width='10%' align='right'>".$tobeAdded_Ytd."</td>";
						echo "</tr>";
						
					}
					
				echo "</table>";*/
		
			break;
			
			case "EMPLIST":
				$qryEmp = "SELECT     empNo, empLastName, empFirstName, empMidName, empDiv, empDepCode,empSecCode,empPosId, empSssNo, empTin
							FROM         tblEmpMast
							WHERE     (empBrnCode = '0001') AND (empStat NOT IN ('RS', 'TR', 'IN'))
							ORDER BY empLastName, empFirstName";
				$resEmp = $scriptsObj->execQry($qryEmp);
				$arrEmp = $scriptsObj->getArrRes($resEmp);
				
				echo "<table border='1' width='100%' style='border-collapse:collapse'>";
				echo "<tr style='height:35px;'>";
					echo "<td width='20%'>EMP. NO.</td>";
					echo "<td width='20%'>EMPLOYEE NAME</td>";
					echo "<td width='20%'>POSITION</td>";
					echo "<td width='20%'>SSS NUMBER</td>";
					echo "<td width='20%'>TIN NUMBER</td>";
				echo "</tr>";
				
				foreach($arrEmp as $arrEmp_val)
				{
					$qryPosDesc = "Select posDesc from tblPosition where posCode='".$arrEmp_val["empPosId"]."' and divCode='".$arrEmp_val["empDiv"]."' and deptCode='".$arrEmp_val["empDepCode"]."' and sectCode='".$arrEmp_val["empSecCode"]."'";
					$rsPosDesc= $scriptsObj->execQry($qryPosDesc);
					$arrPosDesc = $scriptsObj->getSqlAssoc($rsPosDesc);
					
					echo "<tr style='height:35px;'>";
						echo "<td width='20%'>".$arrEmp_val["empNo"]."</td>";
						echo "<td width='20%'>".$arrEmp_val["empLastName"].", ".$arrEmp_val["empFirstName"]." ".$arrEmp_val["empMidName"]."</td>";
						echo "<td width='20%'>".$arrPosDesc["posDesc"]."</td>";
						echo "<td width='20%'>".$arrEmp_val["empSssNo"]."</td>";
						echo "<td width='20%'>".$arrEmp_val["empTin"]."</td>";
					echo "</tr>";
				}
				echo "</table>";
			break;
			
		
			case "YTDCONSOL":
					$qryEarningsGross = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE     compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."' AND (sprtPS IS NULL OR
                      sprtPS = '')";
					$rsEarningsGross= $scriptsObj->execQry($qryEarningsGross);
					$arrEarningsGross[0] = $scriptsObj->getSqlAssoc($rsEarningsGross);	
					
					$qryEarningsTaxEarn = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE     trnTaxCd='Y' and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarningsTaxEarn= $scriptsObj->execQry($qryEarningsTaxEarn);
					$arrEarningsGross[1] = $scriptsObj->getSqlAssoc($rsEarningsTaxEarn);
					
					
					$qryEarningsTaxEarn = "SELECT     SUM(trnAmountD) AS tblEarnings_GrossEarn_total
										FROM         tblDeductionsHist
										WHERE     sprtPS='1' and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarningsTaxEarn= $scriptsObj->execQry($qryEarningsTaxEarn);
					$arrEarningsDedSprt[5] = $scriptsObj->getSqlAssoc($rsEarningsTaxEarn);
					
					
					$qryEarningsEmpBasic = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE    trnCode in (0100, 0111, 0112, 0113, 0801, 0114, 0115) and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarningsEmpBasic= $scriptsObj->execQry($qryEarningsEmpBasic);
					$arrEarningsGross[4] = $scriptsObj->getSqlAssoc($rsEarningsEmpBasic);
					
					
					$qryEarningsSprtAllow = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE    sprtPS='Y' and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarningsSprtAllow= $scriptsObj->execQry($qryEarningsSprtAllow);
					$arrEarningsGross[5] = $scriptsObj->getSqlAssoc($rsEarningsSprtAllow);
					
					$qryEarningsAdvances = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE     (trnCode IN ('8101', '8119')) and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarningsSprtAdvances= $scriptsObj->execQry($qryEarningsAdvances);
					$arrEarningsGross[6] = $scriptsObj->getSqlAssoc($rsEarningsSprtAdvances);
					
					
					$qryEarnings13monthNTax = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE     (trnCode IN ('1000')) and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarnings13monthNTax= $scriptsObj->execQry($qryEarnings13monthNTax);
					$arrEarningsGross[7] = $scriptsObj->getSqlAssoc($rsEarnings13monthNTax);
				
					$qryEarnings13monthAdvances = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE     (trnCode IN ('1100')) and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarnings13monthAdvances= $scriptsObj->execQry($qryEarnings13monthAdvances);
					$arrEarningsGross[8] = $scriptsObj->getSqlAssoc($rsEarnings13monthAdvances);
					
					
					$qryEarnings13monthTax = "SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
										FROM         tblEarningsHist
										WHERE     (trnCode IN ('1010')) and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsEarnings13monthTax= $scriptsObj->execQry($qryEarnings13monthTax);
					$arrEarningsGross[9] = $scriptsObj->getSqlAssoc($rsEarnings13monthTax);
					
					
					$qryMtdGovt = "SELECT     SUM(sssEmp+phicEmp+hdmfEmp) AS mtdGovt
										FROM        tblMtdGovtHist
										WHERE      compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsMtdGovt = $scriptsObj->execQry($qryMtdGovt);
					$arrMtdGovt[2] = $scriptsObj->getSqlAssoc($rsMtdGovt);
						
						
						$qryDedGovt = "SELECT     SUM(trnAmountD) AS dedGovt
										FROM         tblDeductionsHist
										WHERE    trnCode in ( 5200, 5300, 5400) and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsDedGovt= $scriptsObj->execQry($qryDedGovt);
					$arrDedGovt[2] = $scriptsObj->getSqlAssoc($rsDedGovt);					


					$qryDedTax = "SELECT     SUM(trnAmountD) AS dedGovt
										FROM         tblDeductionsHist
										WHERE    trnCode in (5100) and compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsDedTax= $scriptsObj->execQry($qryDedTax);
					$arrDedGovt[3] = $scriptsObj->getSqlAssoc($rsDedTax);
					
					
					
					$qryPaySummary = "SELECT count(*), sum(grossEarnings) as paySumGross, sum(taxableEarnings) as paySumTaxableEarn,
										sum(totDeductions) as paySumTotDed, sum(netSalary) as paySumNetSal, 
										sum(taxWitheld) as paySumtaxWitheld,
										sum(sprtAllow) as paySumSprtAllow, sum(sprtAllowAdvance) as paySumSprtAllowAdvance, 
										sum(empBasic) as paySumempBasic, sum(emp13thMonthNonTax) as paySum13thMonthNon,
										sum(emp13thMonthTax) as paySum13thMonTax, sum(emp13thAdvances) as paySum13thMonthAdvance
									FROM tblPayrollSummaryHist 
									WHERE compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsPaySummary= $scriptsObj->execQry($qryPaySummary);
					$arrPaySummary = $scriptsObj->getSqlAssoc($rsPaySummary);	

					$qryYtdData ="Select SUM(YtdGross) as YtdGross, SUM(YtdTaxable) as YtdTaxable, SUM(YtdGovDed) as YtdGovDed, SUM(YtdTax) as YtdTax, SUM(Ytd13NBonus) as Ytd13NBonus, SUM(YTd13NAdvance) as YTd13NAdvance, 
									SUM(YtdTx13NBonus) as YtdTx13NBonus, SUM(YtdBasic) as YtdBasic, SUM(sprtAllow) as sprtAllow, SUM(sprtAdvance) as sprtAdvance, 
									SUM(YtdGovDedMinWage) as YtdGovDedMinWage, SUM(YtdGovDedAbvWage) as YtdGovDedAbvWage
									FROM tblYtdDataHist 
									WHERE compCode='".$_SESSION["company_code"]."' and pdYear='".date("Y")."'";
					$rsYtdData= $scriptsObj->execQry($qryYtdData);

					$arrYtdData = $scriptsObj->getSqlAssoc($rsYtdData);	
					
				
					$array_desc = array('Gross Earnings','Taxable Earnings', 'Governmentals(Abv. MinWage)', 'Governmentals(Bel. MinWage)', 'Witholding Tax', 'Emp. Basic', 'Separate Allowance', 'Advances', '13th Month Non Taxable', '13th Month Advances', '13th Month Taxable');
					$array_paysum_fields = array('paySumGross','paySumTaxableEarn', '', '', 'paySumtaxWitheld', 'paySumempBasic', 'paySumSprtAllow', 'paySumSprtAllowAdvance', 'paySum13thMonthNon', 'paySum13thMonthAdvance', 'paySum13thMonTax');
					$array_ytddata_fields = array('YtdGross','YtdTaxable', 'YtdGovDed', 'YtdGovDedMinWage','YtdTax', 'YtdBasic', 'sprtAllow', 'sprtAdvance', 'Ytd13NBonus', 'YTd13NAdvance', 'paySum13thMonTax');
					
					
					echo "<table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							echo "<td>Description</td>";
							echo "<td>tblEarningsHist</td>";
							echo "<td>tblDeductions - SprtAllowance</td>";
							echo "<td>tblPayrollSummaryHist</td>";
							echo "<td>tblYtdDataHist</td>";
							echo "<td>tblMtdGovtHist</td>";
							echo "<td>tblDeductionsHist</td>";
							echo "<td>Payroll Recon</td>";
						echo "</tr>";
						
						for($i = 0; $i<=sizeof($array_desc); $i++)
						{
							echo "<tr style='height:35px;'>";
								echo "<td>".$array_desc[$i]."</td>";
								echo "<td align='right'>".number_format($arrEarningsGross[$i]["tblEarnings_GrossEarn_total"],2)."</td>";
								echo "<td align='right'>".number_format($arrEarningsDedSprt[$i]["tblEarnings_GrossEarn_total"],2)."</td>";
								echo "<td align='right'>".number_format($arrPaySummary[$array_paysum_fields[$i]],2)."</td>";
								echo "<td align='right'>".number_format($arrYtdData[$array_ytddata_fields[$i]],2)."</td>";
								echo "<td align='right'>".number_format($arrMtdGovt[$i]["mtdGovt"],2)."</td>";
								echo "<td align='right'>".number_format($arrDedGovt[$i]["dedGovt"],2)."</td>";
								echo "<td align='right'>".number_format($arrDedTax[$i]["dedGovt"],2)."</td>";
							echo "</tr>";
						}
					echo "</table>";
			break;
			
			case "PPCIJR":
				$employee_nos = "010001478,030001579,480000030,120001216,010002010,130000913,200001531,140001295";
				
				
				$qryEmp = "SELECT     empLastName, empFirstName, payPeriod.pdPayable as payDate, payHist.pdYear as pdYear, payHist.pdNumber as pdNum, payHist.empNo as empNo, 
									  payHist.empTeu as empTeu, payHist.empBrnCode as empBrnCode, deptDesc as deptDesc, empMrate as empMrate, 
									  empDrate as empDrate, grossEarnings as grossEarn, taxWitheld as taxWitheld, netSalary as netSal
							FROM      tblPayrollSummaryHist payHist, tblPayPeriod payPeriod, tblDepartment dept, tblEmpMast empMast
							WHERE     (payHist.empNo in (".$employee_nos.")) AND (payHist.pdYear = '2010') AND payHist.pdNumber in (23,24) AND (payHist.compCode = '".$_SESSION["company_code"]."') AND 
									  (payPeriod.compCode = '".$_SESSION["company_code"]."') AND dept.compCode = '".$_SESSION["company_code"]."' AND 
									   payHist.empDivCode = divCode AND payHist.empDepCode = dept.deptCode AND
									   payHist.pdNumber = payPeriod.pdNumber AND payHist.pdYear = payPeriod.pdYear AND payHist.payCat = payPeriod.payCat 
									   AND payHist.payGrp = payPeriod.payGrp and deptLevel=2 and empMast.compCode='".$_SESSION["company_code"]."' 
									   and payHist.empNo=empMast.empNo
									   order by payDate,empNo";
				$resEmp = $scriptsObj->execQry($qryEmp);
				$arrEmp = $scriptsObj->getArrRes($resEmp);


				$arrayFields = array('PAYDATE', 'EMPLOYEE ID#', 'TAX STATUS','BRANCH CODE', 'DEPARTMENT', 'SALARY', 'DAILY RATE',	'ABSENCES AMOUNT',	'TARDY AMOUNT',	'UNDERTIME AMOUNT',	'OVERTIME HOURS',	'GROSS INCOME',	'WITHOLDING TAX',	'EMPLOYEE SSS',	'EMPLOYEE MCR',	'EMPLOYEE PAG-IBIG', 'DEDUCTIONS','NET SALARY', 'EMPLOYER SSS',	'EMPLOYER MCR',	'EMPLOYER EC', 'EMPLOYER PAG-IBIG');
				$arrayFields_val = array('payDate', 'empNo',  'empTeu','empBrnCode', 'deptDesc', 'empMrate', 'empDrate',	'',	'',	'',	'',	'grossEarn',	'taxWitheld','','',	'', '','netSal', '','',	'', '');


				//PMAS
				echo "<b>P_PAYMAS</b><br>";
				echo "<table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrayFields); $i++)
									echo "<td>".$arrayFields[$i]."</td>";
						echo "</tr>";
						
						$in_array = array("7","8","9");
						$in_array_earn = array("","","","","","","","0113","0111","0112");
						
						$in_array_mtd = array("13","14","15",'18','19','20','21');
						foreach($arrEmp as $arrEmp_val)
						{
							$qrychkEarnings = "SELECT trnCode,sum(trnAmountE) as sumEarn
											FROM tblEarningsHist 
											WHERE compCode='".$_SESSION["company_code"]."' AND pdYear='2010' AND pdNumber='".$arrEmp_val["pdNum"]."' AND empNo='".$arrEmp_val["empNo"]."'
											GROUP BY trnCode;";	
							$reschkEarnings = $scriptsObj->execQry($qrychkEarnings);
							$arrchkEarnings[$arrEmp_val["pdNum"]] = $scriptsObj->getArrRes($reschkEarnings);
							
							foreach($arrchkEarnings[$arrEmp_val["pdNum"]] as $arrchkEarnings_val)
								$trnEarn[$arrEmp_val["pdNum"].$arrchkEarnings_val["trnCode"]] = $arrchkEarnings_val["sumEarn"];
							
							$qrychkEarningsOt = "SELECT sum(trnAmountE) as sumEarnOt
											FROM tblEarningsHist 
											WHERE compCode='".$_SESSION["company_code"]."' AND pdYear='2010' AND pdNumber='".$arrEmp_val["pdNum"]."' 
											AND empNo='".$arrEmp_val["empNo"]."' and trnCode in (0221,0327,0222,0233,0328,0338,0223,0234,0329,0339,0224,0235,0340,0225,0236,0331,0341,0226,0237,0332,0342,0802)
											";	
							$reschkEarningsOt = $scriptsObj->getSqlAssoc($scriptsObj->execQry($qrychkEarningsOt));
							$arrchkEarningsOt[$arrEmp_val["pdNum"]] = $reschkEarningsOt["sumEarnOt"];
							
							if($arrEmp_val["pdNum"]=='24')
							{
							$qrychkMtdGovt = "SELECT *
											FROM tblMtdGovtHist 
											WHERE compCode='".$_SESSION["company_code"]."' AND pdYear='2010' AND 
											pdMonth='".date("m", strtotime($arrEmp_val["payDate"]))."' AND empNo='".$arrEmp_val["empNo"]."'; ";	
							$reschkMtdGovt = $scriptsObj->execQry($qrychkMtdGovt);
							$arrchkMtdGovt = $scriptsObj->getSqlAssoc($reschkMtdGovt);
							$arrchkMtdGovt[13] = $arrchkMtdGovt["sssEmp"];
							$arrchkMtdGovt[14] = $arrchkMtdGovt["phicEmp"];
							$arrchkMtdGovt[15] = $arrchkMtdGovt["hdmfEmp"];
							$arrchkMtdGovt[18] = $arrchkMtdGovt["sssEmplr"];
							$arrchkMtdGovt[19] = $arrchkMtdGovt["phicEmplr"];
							$arrchkMtdGovt[20] = $arrchkMtdGovt["ec"];
							$arrchkMtdGovt[21] = $arrchkMtdGovt["hdmfEmplr"];
							
							$arrYtdMtd[$arrEmp_val["empNo"]] = $arrchkMtdGovt["sssEmp"] + $arrchkMtdGovt["phicEmp"] +$arrchkMtdGovt["hdmfEmp"];
							
							}
							
							$qryDed = "SELECT sum(trnAmountD) as trnAmntD
										FROM tblDeductionsHist
										WHERE compCode='".$_SESSION["company_code"]."' AND pdYear='2010' 
											AND empNo='".$arrEmp_val["empNo"]."' and pdNumber='".$arrEmp_val["pdNum"]."'
											AND  (trnCode NOT IN (5100, 5200, 5300, 5400));";
							$reschkDed = $scriptsObj->execQry($qryDed);
							$arrchkDed = $scriptsObj->getSqlAssoc($reschkDed);
							
							
							echo "<tr style='height:35px;'>";
							for($i_f=0; $i_f<sizeof($arrayFields_val); $i_f++)
							{
								
								if(in_array($i_f,$in_array))
									echo "<td>".$trnEarn[$arrEmp_val["pdNum"].$in_array_earn[$i_f]]."</td>";
								elseif($i_f=='10')
									echo "<td>".$arrchkEarningsOt[$arrEmp_val["pdNum"]]."</td>";
								elseif(in_array($i_f,$in_array_mtd))
									echo "<td>".$arrchkMtdGovt[$i_f]."</td>";
								elseif($i_f=='16')
									echo "<td>".$arrchkDed ["trnAmntD"]."</td>";	
								elseif($i_f=='0')
									echo "<td>".date("m/d/Y", strtotime($arrEmp_val[$arrayFields_val[$i_f]]))."</td>";	
								else
									echo "<td>".$arrEmp_val[$arrayFields_val[$i_f]]."</td>";
							}
							echo "</tr>";
						}
						
				echo "</table>";
				
				//YTD
				$qryYtdData = "SELECT	payHist.empNo as empNo,payHist.pdYear,month(payPeriod.pdPayable) as payDate,Sum(grossEarnings) as grossEarn, sum(taxWitheld) as taxWitheld,sum(empBasic) as empBasic
								FROM tblPayrollSummaryHist payHist, tblPayPeriod payPeriod
								WHERE (payHist.empNo in (".$employee_nos.")) AND (payHist.pdYear = '2010') AND (payHist.compCode = '".$_SESSION["company_code"]."') 
										AND (payPeriod.compCode = '".$_SESSION["company_code"]."') AND payHist.pdNumber = payPeriod.pdNumber 
										AND payHist.pdYear = payPeriod.pdYear AND payHist.payCat = payPeriod.payCat 
										AND payHist.payGrp = payPeriod.payGrp and payHist.pdNumber in (23,24)
								GROUP BY payHist.pdYear,month(payPeriod.pdPayable), empNo
								ORDER BY empNo,month(payPeriod.pdPayable);";
				$resYtdData = $scriptsObj->execQry($qryYtdData);
				$arrYtdData = $scriptsObj->getArrRes($resYtdData);
				
				
				$arrayFields_YtdData = array('EMPLOYEE ID#','MONTH','YEAR','YTD GROSS','YTD TAX','YTD SSS/MCR/PAG-IBIG','YTD BASIC');
				$arrayFields_YtdDataArr = array('empNo','payDate','pdYear','grossEarn','taxWitheld','','empBasic');
				
				echo "<br><b>YTD</b>";
				echo "<br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrayFields_YtdData); $i++)
								echo "<td>".$arrayFields_YtdData[$i]."</td>";
						echo "</tr>";
				
				foreach($arrYtdData as $arrYtdData_val)
				{
					echo "<tr style='height:35px;'>";
					for($i=0; $i<sizeof($arrayFields_YtdDataArr); $i++)
					{
						if($i!=5)
							echo "<td>".$arrYtdData_val[$arrayFields_YtdDataArr[$i]]."</td>";
						else
							echo "<td>".$arrYtdMtd[$arrYtdData_val["empNo"]]."</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
				
				
				//ALLOWANCE			
				$qryAllow = "SELECT empNo, allowDesc, allowAmt 
							FROM tblAllowance allow, tblAllowType allowType
							WHERE empNo in (".$employee_nos.") AND allow.compCode='".$_SESSION["company_code"]."' AND allowStat='A' and allowType.compCode='".$_SESSION["company_code"]."' 
							and allow.allowCode=allowType.allowCode";
				$resAllow= $scriptsObj->execQry($qryAllow);
				$arrAllow = $scriptsObj->getArrRes($resAllow);			
							
				echo "<br><b>ALLOWANCE DETAIL</b>";
				echo "<br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							echo "<td>EMP. NO.</td>";
							echo "<td>ALLOWANCE TYPE</td>";
							echo "<td>AMOUNT</td>";
						echo "</tr>";
						
						foreach($arrAllow as $arrAllow_val)
						{
							echo "<tr style='height:35px;'>";
								echo "<td>".$arrAllow_val["empNo"]."</td>";
								echo "<td>".$arrAllow_val["allowDesc"]."</td>";
								echo "<td>".$arrAllow_val["allowAmt"]."</td>";
							echo "</tr>";
						}
				echo "</table>";
				
				
				//LOAN HEADER
				$qryLoanHdr = "SELECT     empLoans.empNo, empMast.empLastName, empMast.empFirstName, empMast.empMidName, loanType.lonTypeDesc, empLoans.lonRefNo, empLoans.lonWidInterst, empLoans.lonAmt, empLoans.lonPayments, empLoans.lonCurbal, empLoans.lonStart
								FROM  tblEmpLoans empLoans INNER JOIN
                      					tblEmpMast empMast ON empLoans.empNo = empMast.empNo INNER JOIN
                      					tblLoanType loanType ON empLoans.lonTypeCd = loanType.lonTypeCd
								WHERE     (empLoans.compCode = '".$_SESSION["company_code"]."') AND (empMast.compCode = '".$_SESSION["company_code"]."') 
										AND (loanType.compCode = '".$_SESSION["company_code"]."') AND (empLoans.empNo in (".$employee_nos."))";
				$resLoanHdr = $scriptsObj->execQry($qryLoanHdr);
				$arrLoanHdr = $scriptsObj->getArrRes($resLoanHdr);			
				
				$arrFields_LoanHdr = array("EMP. NO.", "EMP. LAST NAME", "EMP. FIRST NAME", "EMP. MID NAME", "LOAN TYPE DESC.", "LOAN REF. NO.", "LOAN WID INTEREST",	"LOAN AMOUNT", "LOAN PAYMENTS" , "LOAN CUR. BALANCE", "LOAN START");
				$arrFields_arrLoanHdr = array("empNo", "empLastName", "empFirstName", "empMidName", "lonTypeDesc", "lonRefNo", "lonWidInterst", "lonAmt", "lonPayments", "lonCurbal", "lonStart");
				echo "<br><b>LOAN HEADER </b>";
				echo "<br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_LoanHdr); $i++)
								echo "<td>".$arrFields_LoanHdr[$i]."</td>";
						echo "</tr>";
						
						foreach($arrLoanHdr as $arrLoanHdr_val)
						{
							echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_arrLoanHdr); $i++)
							{
								if($i!=10)
									echo "<td>".$arrLoanHdr_val[$arrFields_arrLoanHdr[$i]]."</td>";
								else
									echo "<td>".date("m/d/Y", strtotime($arrLoanHdr_val[$arrFields_arrLoanHdr[$i]]))."</td>";
							}
							echo "</tr>";
						}
				echo "</table>";
				
				
				
				//LOAN DETAIL
				$qryLoanDtl = "SELECT     empMast.empNo, empMast.empLastName, empMast.empFirstName, empMast.empMidName, loanType.lonTypeDesc, empLoans.lonRefNo, empLoans.pdYear, empLoans.pdNumber, empLoans.trnAmountD, empLoans.ActualAmt, empLoans.dedTag, empLoans.lonLastPay, empLoans.dedtoAdv
								FROM    tblEmpLoansDtlHist empLoans INNER JOIN
                      					tblEmpMast empMast ON empLoans.empNo = empMast.empNo INNER JOIN
                      					tblLoanType loanType ON empLoans.lonTypeCd = loanType.lonTypeCd
								WHERE     (empMast.compCode = '".$_SESSION["company_code"]."') AND (loanType.compCode = '".$_SESSION["company_code"]."') 
										AND (empLoans.empNo in (".$employee_nos.")) AND (empLoans.compCode = '".$_SESSION["company_code"]."');";
				$resLoanDtl = $scriptsObj->execQry($qryLoanDtl);
				$arrLoanDtl = $scriptsObj->getArrRes($resLoanDtl);			
				
				$arrFields_LoanDtl = array("EMP. NO.", "EMP. LAST NAME", "EMP. FIRST NAME", "EMP. MID NAME", "LOAN TYPE DESC.", "LOAN REF. NO.", "LOAN YEAR DEDUCTED",	"LOAN PD NUMBER DEDUCTED", "LOAN DEDUCTED" , "LOAN ACTUAL AMT.", "LOAN DED. TAG (if Y = deducted; N = Not Deducted)", "LOAN DED. TO ADVANCES (if Y = deducted to advances)" );
				$arrFields_arrLoanDtl = array("empNo", "empLastName", "empFirstName", "empMidName", "lonTypeDesc", "lonRefNo", "pdYear", "pdNumber", "trnAmountD", "ActualAmt", "dedTag", "dedtoAdv");
				echo "<br><b>LOAN DETAIL </b>";
				echo "<br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_LoanDtl); $i++)
								echo "<td>".$arrFields_LoanDtl[$i]."</td>";
						echo "</tr>";
						
						foreach($arrLoanDtl as $arrLoanDtl_val)
						{
							echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_arrLoanDtl); $i++)
								echo "<td>".$arrLoanDtl_val[$arrFields_arrLoanDtl[$i]]."</td>";
							echo "</tr>";
						}
				echo "</table>";
				
				
				/*//GOV LOANS
				 $qryGovLoans = "SELECT     tblDeductionsHist.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblLoanType.lonTypeShortDesc, 
												  tblEmpLoans.lonRefNo, tblEmpLoans.lonWidInterst, tblDeductionsHist.trnAmountD, 
												  tblEmpLoans.lonPayments + tblDeductionsHist.trnAmountD AS lonPayments, tblEmpLoans.lonCurbal + tblDeductionsHist.trnAmountD * - 1 AS lonCurbal,
												   tblDeductionsHist.pdYear, tblEmpLoans.lonGranted, tblDeductionsHist.pdNumber, tblEmpLoans.lonStart
									FROM         tblDeductionsHist LEFT OUTER JOIN
												  tblEmpMast ON tblDeductionsHist.compCode = tblEmpMast.compCode AND tblDeductionsHist.empNo = tblEmpMast.empNo LEFT OUTER JOIN
												  tblLoanType INNER JOIN
												  tblEmpLoans ON tblLoanType.compCode = tblEmpLoans.compCode AND tblLoanType.lonTypeCd = tblEmpLoans.lonTypeCd ON 
												  tblDeductionsHist.compCode = tblEmpLoans.compCode AND tblDeductionsHist.empNo = tblEmpLoans.empNo AND tblEmpLoans.lonTypeCd IN (22)
									AND  tblEmpLoans.lonStat IN ('C', 'T')
									WHERE     (tblDeductionsHist.trnCode IN (N'5901','5902'))  AND tblDeductionsHist.empNo in (".$employee_nos.") and tblDeductionsHist.pdYear='2010'
									order by  tblDeductionsHist.empNo, pdNumber";
				$resGovLoans = $scriptsObj->execQry($qryGovLoans);
				$arrGovLoans = $scriptsObj->getArrRes($resGovLoans);	
				
				$arrFields_GovLoans = array("EMP. NO.",	"EMP. LAST NAME", "EMP. FIRST NAME", "EMP. MID. NAME",	"LOAN TYPE SHORT DESC",	"LOAN REF NO.", "LOANWIDINTEREST", "ACTUAL AMT.", "LOAN PAYMENTS", "LOAN CURRENT BAL.", "PD YEAR","LOAN GRANTED","PDNUMBER", "LOAN START");
				$arrFields_arrGovLoans = array("empNo",	"empLastName", "empFirstName", "empMidName",	"lonTypeShortDesc",	"lonRefNo",	"lonWidInterst",	"trnAmountD",	"lonPayments",	"lonCurbal",	"pdYear",	"lonGranted",	"pdNumber",	"lonStart");
				
				
				echo "<br><br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_GovLoans); $i++)
								echo "<td>".$arrFields_GovLoans[$i]."</td>";
						echo "</tr>";
						
						foreach($arrGovLoans as $arrGovLoans_val)
						{
							echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_arrGovLoans); $i++)
									echo "<td>".$arrGovLoans_val[$arrFields_arrGovLoans[$i]]."</td>";
							echo "</tr>";
						}
				echo "</table>";
				
				//LOAN ADJUSTMENT
				$qryGovLoansAdj = "SELECT     tblEmpLoansDtlHist.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblLoanType.lonTypeShortDesc, 
														  tblEmpLoansDtlHist.lonRefNo, tblEmpLoans.lonWidInterst, tblEmpLoansDtlHist.ActualAmt, tblEmpLoans.lonPayments, tblEmpLoans.lonCurbal, 
														  tblEmpLoansDtlHist.pdYear, tblEmpLoans.lonGranted, tblEmpLoansDtlHist.pdNumber, tblEmpLoans.lonStart
									FROM         tblEmpLoansDtlHist INNER JOIN
														  tblEmpMast ON tblEmpLoansDtlHist.compCode = tblEmpMast.compCode AND tblEmpLoansDtlHist.empNo = tblEmpMast.empNo INNER JOIN
														  tblLoanType ON tblEmpLoansDtlHist.compCode = tblLoanType.compCode AND tblEmpLoansDtlHist.lonTypeCd = tblLoanType.lonTypeCd INNER JOIN
														  tblEmpLoans ON tblEmpLoansDtlHist.compCode = tblEmpLoans.compCode AND tblEmpLoansDtlHist.empNo = tblEmpLoans.empNo AND 
														  tblEmpLoansDtlHist.lonTypeCd = tblEmpLoans.lonTypeCd AND tblEmpLoansDtlHist.lonRefNo = tblEmpLoans.lonRefNo
									WHERE     (tblEmpLoansDtlHist.pdYear = '2010') AND (tblEmpLoansDtlHist.lonTypeCd IN (11, 12, 21, 22)) AND (tblEmpLoansDtlHist.ManualTag IS NULL) AND tblEmpLoansDtlHist.empNo in (".$employee_nos.")
									ORDER BY tblEmpLoansDtlHist.lonTypeCd, tblEmpMast.empLastName, tblEmpMast.empFirstName 
																		";
				$resGovLoansAdj = $scriptsObj->execQry($qryGovLoansAdj);
				$arrGovLoansAdj = $scriptsObj->getArrRes($resGovLoansAdj);	
				
				$arrFields_GovLoansAdj = array("EMP. NO.",	"EMP. LAST NAME", "EMP. FIRST NAME", "EMP. MID. NAME",	"LOAN TYPE SHORT DESC",	"LOAN REF NO.", "LOANWIDINTEREST", "ACTUAL AMT.", "LOAN PAYMENTS", "LOAN CURRENT BAL.", "PD YEAR","LOAN GRANTED","PDNUMBER", "LOAN START");
				$arrFields_arrGovLoansAdj = array("empNo",	"empLastName", "empFirstName", "empMidName",	"lonTypeShortDesc",	"lonRefNo",	"lonWidInterst",	"ActualAmt",	"lonPayments",	"lonCurbal",	"pdYear",	"lonGranted",	"pdNumber",	"lonStart");
				
				
				echo "<br><br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_GovLoansAdj); $i++)
								echo "<td>".$arrFields_GovLoansAdj[$i]."</td>";
						echo "</tr>";
						
						foreach($arrGovLoansAdj as $arrGovLoansAdj_val)
						{
							echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_arrGovLoansAdj); $i++)
									echo "<td>".$arrGovLoansAdj_val[$arrFields_arrGovLoansAdj[$i]]."</td>";
							echo "</tr>";
						}
				echo "</table>";*/
				
				//PAF
				$qryPafHist = "SELECT     empNo, old_empMrate, old_empDrate, old_empHrate, new_empMrate, new_empDrate, new_empHrate, effectivitydate, dateadded, dateupdated,  datereleased
								FROM   tblPAF_PayrollRelatedhist
								WHERE    (empNo IN (".$employee_nos.")) and old_empMrate is not null
								ORDER BY empNo";
				$resPafHist = $scriptsObj->execQry($qryPafHist);
				$arrPafHist = $scriptsObj->getArrRes($resPafHist);				
				
				$arrFields_PafHist = array("EMP. NO.", "OLD_EMPMRATE", "OLD_EMPDRATE", "OLD_EMPHRATE", "NEW_EMPMRATE", "NEW_EMPDRATE", "NEW_EMPHRATE",	"EFFECTIVITY DATE", "DATE ADDED" , "DATE UPDATED", "DATE RELEASED");
				$arrFields_arrPafHist = array("empNo", "old_empMrate", "old_empDrate", "old_empHrate", "new_empMrate", "new_empDrate", "new_empHrate", "effectivitydate", "dateadded", "dateupdated",  "datereleased");
				echo "<br><b>PAF DETAIL</b>";
				echo "<br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_PafHist); $i++)
								echo "<td>".$arrFields_PafHist[$i]."</td>";
						echo "</tr>";
						
						foreach($arrPafHist as $arrPafHist_val)
						{
							echo "<tr style='height:35px;'>";
							for($i=0; $i<sizeof($arrFields_arrPafHist); $i++)
							{
								if($i<7)
									echo "<td>".$arrPafHist_val[$arrFields_arrPafHist[$i]]."</td>";
								else
									echo "<td>".date("m/d/Y", strtotime($arrPafHist_val[$arrFields_arrPafHist[$i]]))."</td>";
							}
							echo "</tr>";
						}
				echo "</table>";
				
			break;
			
			case "UPDTEYTDTAXREFUND":
				$qryUpdateTax = "Select * from UpdateEmpTaxRefund order by empNo";
				$resUpdateTax = $scriptsObj->execQry($qryUpdateTax);
				$arrUpdateTax = $scriptsObj->getArrRes($resUpdateTax);
				
				foreach($arrUpdateTax as $arrUpdateTax_val)
				{
					echo $qryUpdated = "Update tblYtdDataHist_Year2010 set updatedYtdTaxable='".$arrUpdateTax_val["UpdatedYtdTaxable"]."', updatedYtdGovDed='".$arrUpdateTax_val["UpdatedYtdGovDed"]."', updatedYtdTax='".$arrUpdateTax_val["UpdatedYtdTax"]."' where empNo='".$arrUpdateTax_val["empNo"]."'; <br>";
				}	
			break;
			
			case "MIGJRBRANCH":
				$tblExcelBranch = "";
				
				$qryInsBranch = "Select * from ".$tblExcelBranch."";
				$resInsBranch = $scriptsObj->execQry($qryInsBranch);
				$arrInsBranch = $scriptsObj->getArrRes($resInsBranch);
				
				foreach($arrInsBranch as $arrInsBranch_val)
				{
					$insBranch = "Insert into tblBranch(compCode,brnCode,brnDesc,brnShortDesc,brnRegion,
														brnAddr1,minWage,brnSignatory,
														brnSignTitle,brnDefGrp,brnStat,compglCode,compglCodeHO,
														glCodeHO,glCodeStr,brnLoc,coCtr,
														brnShortName)
								 values('".$_SESSION["company_code"]."', '".$arrInsBranch_val["brnCode"]."', '".$arrInsBranch_val["brnDesc"]."', '".$arrInsBranch_val["brnShortDesc"]."', '".$arrInsBranch_val["brnRegion"]."'
								 		, '".$arrInsBranch_val["brnAddr1"]."', '".sprintf("%01.2f", $arrInsBranch_val["minWage"])."', '".$arrInsBranch_val["brnSignatory"]."'
										, '".$arrInsBranch_val["brnSignTitle"]."', '".$arrInsBranch_val["brnDefGrp"]."', 'A', '".$arrInsBranch_val[""]."', '".$arrInsBranch_val[""]."'
										, '".$arrInsBranch_val[""]."', '".$arrInsBranch_val[""]."', 'ST', '5', '".$arrInsBranch_val["brnShortDesc"]."');";
										
				}	
			break;
			
			
			/*Hi!
				
				I just read your email, and I am well pleasure to give my resume, in fact, one of my officemate(s) is also interested in pursuing her career
				at Accenture.
				
				Kindly refer to the attachment(s) for our resume. 
				
				
				Hope our knowledge and skills will suit to your company's qualifications and opportunities.
				
				
				Thank You.
			*/
			
			
			case "GETPASS":
				 $qryUsers = "SELECT     users.empNo, empMast.empLastName, empMast.empFirstName, userPass,  pages201, userLevel, category 
							FROM         tblUsers users INNER JOIN
										  tblEmpMast empMast ON users.empNo = empMast.empNo 
							WHERE          Pages201 is not null and userLevel<>1 
							ORDER BY userLevel,empLastName ";
				$resUsers = $scriptsObj->execQry($qryUsers);
				$arrUsers= $scriptsObj->getArrRes($resUsers);
				
				echo "<br><b>USER DETAIL</b>";
				echo "<br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							//echo "<td>Emp. No.</td>";
							echo "<td>Emp. Name</td>";
							echo "<td>Access Branch</td>";
							//echo "<td>Password</td>";
							echo "<td>User Level</td>";
							echo "<td>User Pay Cat Access</td>";
							echo "<td>Module Access</td>";
							
						echo "</tr>";
				foreach($arrUsers as $arrUsers_val)
				{
					echo "<tr style='height:35px;'>";
							
							//echo "<td valign='top'>".$arrUsers_val["empNo"]."</td>";
							echo "<td valign='top'>".$arrUsers_val["empLastName"].", ".$arrUsers_val["empFirstName"]."</td>";
							
							$qryBranch = "Select brnDesc from tblUserBranch userBranch, tblBranch brnch
											where  userBranch.compCode='".$_SESSION["company_code"]."' and brnch.compCode='".$_SESSION["company_code"]."'
											and userBranch.brnCode=brnch.brnCode and empNo='".$arrUsers_val["empNo"]."' order by brnDesc";
							$resBranch = $scriptsObj->execQry($qryBranch);
							$arrBranch = $scriptsObj->getArrRes($resBranch);
							
							echo "<td valign='top'>";
							foreach($arrBranch as $arrBranch_val)
								echo $arrBranch_val["brnDesc"]."<br>";
							
							echo "</td>";
														
							//echo "<td valign='top'>".base64_decode($arrUsers_val["userPass"])."</td>";
							echo "<td valign='top'>".($arrUsers_val["userLevel"]=='2'?"Super User":"User")."</td>";
							
							$qryPayCat = "Select payCatDesc from tblPayCat where payCat in (".$arrUsers_val["category"].") order by payCatDesc";
							$resPayCat = $scriptsObj->execQry($qryPayCat);
							$arrPayCat = $scriptsObj->getArrRes($resPayCat);
							
							echo "<td valign='top'>";
							foreach($arrPayCat as $arrPayCat_val)
								echo $arrPayCat_val["payCatDesc"]."<br>";
							
							echo "</td>";
							
							if($arrUsers_val["pages201"]!="")
							{
								$qryMenu = "Select label from tbl201Menu where moduleId in (".$arrUsers_val["pages201"].") order by menuOrder, moduleOrder; ";
								$resMenu = $scriptsObj->execQry($qryMenu);
								$arrMenu = $scriptsObj->getArrRes($resMenu);
								
								echo "<td valign='top'>";
								foreach($arrMenu as $arrMenu_val)
									echo $arrMenu_val["label"]."<br>";
									
								echo "</td>";
							
							}
							
							
						echo "</tr>";
				}
				echo "</table>";
			break;
			
			case "GLDEPTUPDATE":
				$arrayGlCodes = array(	'ACCOUNTING'=>'ACC',
										'ADMINISTRATION'=>'ADM',
										'AUDIT'=>'ADT',
										'CASH MANAGEMENT'=>'FIN',
										'HUMAN RESOURCES  '=>'PRS',
										'INVENTORY CONTROL'=>'IC',
										'MANAGEMENT INFORMATIONS SYSTEM'=>'MIS',
										'MARKETING'=>'MKTG',
										'MERCHANDISING (HOME)'=>'MDS HOME',
										'MERCHANDISING (SUPERMARKET)'=>'MDS',
										'TRADE PAYABLES'=>'TP',
										'STORE OPERATIONS'=>'SOP',
										'TREASURY CREDIT & COLLECTION'=>'TCC',
										'WAREHOUSE'=>'WHS',
										'LOGISTICS'=>'LOG');
				
				/*
				'101'=>'ACCOUNTING',
									'105'=>'ADMINISTRATION',
									'108'=>'AUDIT',
									'121'=>'BUDGET',
									'111'=>'CASH MANAGEMENT',
									'123'=>'CREDIT & COLLECTION',
									'112'=>'EXECUTIVE',
									'112'=>'EXECUTIVE OFFICE',
									'116'=>'FINANCIAL CONTROL',
									'110'=>'HUMAN RESOURCES',
									'117'=>'INVENTORY CONTROL',
									'118'=>'LEASING',
									'120'=>'LEGAL',
									'113'=>'LOGISTICS',
									'107'=>'MANAGEMENT INFORMATIONS SYSTEM',
									'107'=>'MANAGEMENT INFORMATIONS SYSTEM',
									'109'=>'MARKETING',
									'114'=>'MERCHANDISING (HOME)',
									'103'=>'MERCHANDISING (SUPERMARKET)',
									'115'=>'PAYABLES',
									'122'=>'PAYROLL',
									'119'=>'PROJECT DEVELOPMENT',
									'104'=>'STORE OPERATIONS',
									'115'=>'TRADE PAYABLES',
									'106'=>'TREASURY',
									'106'=>'TREASURY CREDIT & COLLECTION',
									'102'=>'WAREHOUSE',
									'102'=>'WAREHOUSE'*/

				/*$gettblDepartment = "SELECT     deptGlCode, deptDesc
									FROM         tblDepartment
									WHERE     (deptGlCode IS NOT NULL)
									ORDER BY deptDesc";
				$resGettblDept = $scriptsObj->execQry($gettblDepartment);
				$arrGettblDept = $scriptsObj->getArrRes($resGettblDept);	
				
				foreach($arrGettblDept as $arrGettblDept_val)
				{
					$qryGlCode = "SELECT     *
									FROM         tblGLCodes
									WHERE     (strCode = '1001202') and minCode='".$arrGettblDept_val["deptGlCode"]."';";
					$resGlCode= $scriptsObj->execQry($qryGlCode);
					$arrGlCode = $scriptsObj->getSqlAssoc($resGlCode);	
					
					echo 		$arrGlCode["glCodeDesc"]."=".$arrayGlCodes[$arrGlCode["minCode"]]."="."<br>";
					
				}*/
				
				$qryGlCode = "SELECT     minCode, majCode,glCodeDesc
									FROM         tblGLCodes
									WHERE     (strCode = '1001202')
									ORDER BY glCodeDesc;";
				$resGlCode= $scriptsObj->execQry($qryGlCode);
				$arrGlCode = $scriptsObj->getArrRes($resGlCode);	
				foreach($arrGlCode as $arrGlCode_val)
				{
					$gettblDepartment = "SELECT     deptGlCode, deptDesc
									FROM         tblDepartment
									WHERE     deptGlCode='".$arrGlCode_val["minCode"]."';";
					$resGettblDept= $scriptsObj->execQry($gettblDepartment);
					$arrGlCode = $scriptsObj->getSqlAssoc($resGettblDept);	
					
					echo $qryUpdateDept = "Update tblGlCodes set glCodeDesc='".str_replace($arrayGlCodes[$arrGlCode["deptDesc"]],$arrGlCode["deptDesc"], $arrGlCode_val["glCodeDesc"])."' where minCode='".$arrGlCode_val["minCode"]."' and majCode='".$arrGlCode_val["majCode"]."';<br>";
					//echo $arrGlCode_val["glCodeDesc"]."=".$arrGlCode["deptDesc"]."=".str_replace($arrayGlCodes[$arrGlCode["deptDesc"]],$arrGlCode["deptDesc"], $arrGlCode_val["glCodeDesc"])."<br>";
				}
				
			break;
			
			case "UPDATELOANS":
				//$qryGetEmpLoans = "Select * from tblEmpLoans where compCode='".$_SESSION["company_code"]."' and (lonTypeCd IN (11, 12));";
				$qryGetEmpLoans = "Select lonTypeDesc ,empLastName, empFirstName, empMidName, empStat, empLoans.empNo, empLoans.lonTypeCd, lonRefNo, lonAmt,lonGranted 
									from tblEmpLoans empLoans,
									tblEmpmast empMast,
									tblLoanType lonType
									where empLoans.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."' and lonType.compCode='".$_SESSION["company_code"]."'
									and empLoans.empNo=empMast.empNo and empLoans.lonTypeCd=lonType.lonTypeCd
									 and (empLoans.lonTypeCd IN (11, 12)) and lonStat not in ('T', 'C')
									order by lonTypeDesc, empLastName, empFirstName;";
				
				$resGetEmpLoans = $scriptsObj->execQry($qryGetEmpLoans);
				$arrGetEmpLoans = $scriptsObj->getArrRes($resGetEmpLoans);	
				
				$editedRefNo = "<tr style='height:35px;'><td colspan='5'>Reference No. in Paradox not equal with the PG - HRIS Loan Set Up</td></tr>";
				$editedupdateLoanPrin = "<tr style='height:35px;'><td colspan='5'>List of Employees where Loan Amount should be Updated to Loan Principal </td></tr>";
				$editedupdateLoanPrin_noupdates = "<tr style='height:35px;'><td colspan='5'>List of Employees where Loan Amount is same as the Loan Principal </td></tr>";
				$editeddateGranted = "<tr style='height:35px;'><td colspan='5'>Date Granted in Paradox not equal with the PG - HRIS Loan Set Up </td></tr>";
				$disLoanType = "<tr style='height:35px;'><td colspan='5'>Loan Type in Paradox not equal with the PG - HRIS Loan Set Up</td></tr>";
				$notParadox_act = "<tr style='height:35px;'><td colspan='5'>Not in Paradox (ACTIVE)</td></tr>";
				$notParadox_res = "Not in Paradox (RESIGNED)<br>";
				
				
				$ctr_updateLoanPrin =$ctr_updatenoLoanRef = $ctr_updatenoLoanDate = $ctr_updatenoUpdates = $ctr_updatenoUpdates_active = $ctr_updatenoUpdates_res = 1;
				$ctr_updatenoLoanPrin = $ctr = 1;	
				foreach($arrGetEmpLoans as $arrGetEmpLoans_val)
				{
						//echo $arrGetEmpLoans_val["empNo"]."<br>";;
						$empLoanPrincipal_hris = $empLoanPrincipal_paradox = 0;	
						$qryCheckLoans = "SELECT * FROM  PPCI_LOAN_HIST where empNo='".$arrGetEmpLoans_val["empNo"]."' and loanTypeCode='".$arrGetEmpLoans_val["lonTypeCd"]."' and loanRef='".$arrGetEmpLoans_val["lonRefNo"]."' and loanDateGranted='".date("m/d/Y", strtotime($arrGetEmpLoans_val["lonGranted"]))."';";	
						//echo $qryCheckLoans."<br>";;
						//$qryCheckLoans = "SELECT * FROM  PPCI_LOAN_HIST where empNo='".$arrGetEmpLoans_val["empNo"]."' and loanTypeCode='".$arrGetEmpLoans_val["lonTypeCd"]."' and loanRef='".$arrGetEmpLoans_val["lonRefNo"]."';";	
						
						$reschkLoans = $scriptsObj->execQry($qryCheckLoans);
						$arrchkLoans = $scriptsObj->getSqlAssoc($reschkLoans);
				
						if($arrchkLoans["empNo"]!="")
						{
							
							$empLoanPrincipal_hris = sprintf("%01.2f",$arrGetEmpLoans_val["lonAmt"]);
							$empLoanPrincipal_paradox = sprintf("%01.2f",$arrchkLoans["loanPrincipal"]);
						
							if($arrchkLoans["loanPrincipal"]!=0)
							{
								//echo $arrchkLoans["empNo"]."=".$empLoanPrincipal_paradox."!=".$empLoanPrincipal_hris."<br>";
								if ($empLoanPrincipal_paradox!=$empLoanPrincipal_hris)
								{
									//$editedupdateLoanPrin.="<tr style='height:35px;'><td>".$ctr_updateLoanPrin.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".$arrGetEmpLoans_val["lonAmt"]."</td><td>".$arrchkLoans["loanPrincipal"]."</td><td>".$arrchkLoans["loanPrincipal"] ."</td></tr>";
									$qryUpdateLoanAmt.="Update tblEmpLoans set lonAmt='". sprintf("%01.2f",$arrchkLoans["loanPrincipal"])."' where empNo='".$arrGetEmpLoans_val["empNo"]."' and lonTypeCd='".$arrGetEmpLoans_val["lonTypeCd"]."' and lonRefNo='".$arrGetEmpLoans_val["lonRefNo"]."' and lonGranted='".date("m/d/Y", strtotime($arrGetEmpLoans_val["lonGranted"]))."';<br>";
									$ctr_updateLoanPrin++;	
								}
								else
								{
									//$editedupdateLoanPrin_noupdates.="<tr style='height:35px;'><td>".$ctr_updatenoLoanPrin.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".$arrGetEmpLoans_val["lonAmt"]."</td><td>".$arrchkLoans["loanPrincipal"]."</td><td>".$arrchkLoans["loanPrincipal"] ."</td></tr>";
									$ctr_updatenoLoanPrin++;
								}	
									//echo $arrGetEmpLoans_val["empNo"]."=".$empLoanPrincipal_hris."=".$empLoanPrincipal_paradox ."<br>";
							}
							else
							{
								//$editedupdateLoanPrin_noupdates.="<tr style='height:35px;'><td>".$ctr_updatenoLoanPrin.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".$arrGetEmpLoans_val["lonAmt"]."</td><td>".$arrchkLoans["loanPrincipal"]."</td><td>".$arrchkLoans["loanPrincipal"] ."</td></tr>";
								$ctr_updatenoLoanPrin++;
							}
							$ctr++;
							
						}
						else
						{
							$qryCheckLoans_reason = "SELECT * FROM  PPCI_LOAN_HIST where empNo='".$arrGetEmpLoans_val["empNo"]."'";
							$reschkLoans_reason = $scriptsObj->execQry($qryCheckLoans_reason);
							$arrchkLoans_reason = $scriptsObj->getSqlAssoc($reschkLoans_reason);
							
							if(($arrchkLoans_reason["loanTypeCode"]=="11") || ($arrchkLoans_reason["loanTypeCode"]=="12"))
							{
								//Edited Reference No.
								if($arrGetEmpLoans_val["lonRefNo"]!=$arrchkLoans_reason["loanRef"]) 
								{
									//echo $arrGetEmpLoans_val["empNo"]."=>".$arrGetEmpLoans_val["lonRefNo"]."!=".$arrchkLoans_reason["loanRef"]."<br>";
									$qryCheckLoans_update = "SELECT * FROM  PPCI_LOAN_HIST_UPDATED_BY_PAYROLL where empNo='".$arrGetEmpLoans_val["empNo"]."' and curr_hris_loansetup='".$arrGetEmpLoans_val["lonRefNo"]."'";
									$reschkLoans_update = $scriptsObj->execQry($qryCheckLoans_update);
									$arrchkLoans_update = $scriptsObj->getSqlAssoc($reschkLoans_update);
							
									if(($arrchkLoans_update["updated_ref_no"]!="") && ($arrGetEmpLoans_val["lonRefNo"]!=$arrchkLoans_update["updated_ref_no"]))
									{
										//$editedRefNo.="<tr style='height:35px;'><td>".$ctr_updatenoLoanRef.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".$arrGetEmpLoans_val["lonRefNo"]."</td><td>".$arrchkLoans_reason["loanRef"]."</td><td>".($arrchkLoans_update["updated_ref_no"]!=""?$arrchkLoans_update["updated_ref_no"]:"NO UPDATES TO BE MADE")."</td></tr>";
										//echo "Update tblEmpLoans set lonRefNo='". $arrchkLoans_update["updated_ref_no"]."' where empNo='".$arrGetEmpLoans_val["empNo"]."' and lonTypeCd='".$arrGetEmpLoans_val["lonTypeCd"]."' and lonRefNo='".$arrGetEmpLoans_val["lonRefNo"]."';<br>";
										$ctr_updatenoLoanRef++;
									}
								}
								//Check Date Granted
								elseif($arrGetEmpLoans_val["lonGranted"]!=$arrchkLoans_reason["loanDateGranted"])
								{
									$qryCheckLoans_update = "SELECT * FROM  PPCI_LOAN_HIST_UPDATED_BY_PAYROLL where empNo='".$arrGetEmpLoans_val["empNo"]."' and curr_paradox_loansetup='".$arrchkLoans_reason["loanDateGranted"]."'";
									$reschkLoans_update = $scriptsObj->execQry($qryCheckLoans_update);
									$arrchkLoans_update = $scriptsObj->getSqlAssoc($reschkLoans_update);
							
									if($arrchkLoans_update["updated_loan_granted"]!="")
									{	
										echo "Update tblEmpLoans set lonGranted='". date("m/d/Y", strtotime($arrchkLoans_update["updated_loan_granted"]))."' where empNo='".$arrGetEmpLoans_val["empNo"]."' and lonTypeCd='".$arrGetEmpLoans_val["lonTypeCd"]."' and lonRefNo='".$arrGetEmpLoans_val["lonRefNo"]."';<br>";
										//$editeddateGranted.="<tr style='height:35px;'><td>".$ctr_updatenoLoanDate.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".date("m/d/Y", strtotime($arrGetEmpLoans_val["lonGranted"]))."</td><td>".$arrchkLoans_reason["loanDateGranted"]."</td><td>".($arrchkLoans_update["updated_loan_granted"]!=""?date("m/d/Y", strtotime($arrchkLoans_update["updated_loan_granted"])):$qryCheckLoans_update)."</td></tr>";
									
										$ctr_updatenoLoanDate++;
									}
								}
								//EmpLoans LoanType Code = 11, while in Paradox = 12
								else
								{
									//disLoanType.="<tr style='height:35px;'><td>".$ctr_updatenoUpdates.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".$arrGetEmpLoans_val["lonTypeCd"]."</td><td>".$arrchkLoans_reason["loanTypeCode"]."</td><td>NO UPDATES TO BE MADE</td></tr>";
									$ctr_updatenoUpdates++;
								}
							}
							else
							{
								//Not in Paradox
								
								if(($arrGetEmpLoans_val["empStat"]=='RG')||($arrGetEmpLoans_val["empStat"]=='CN')||($arrGetEmpLoans_val["empStat"]=='PR'))
								{
									//$notParadox_act.="<tr style='height:35px;'><td>".$ctr_updatenoUpdates_active.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".$arrGetEmpLoans_val["lonTypeCd"]."</td><td>".$arrchkLoans_reason["loanTypeCode"]."</td><td>NO UPDATES TO BE MADE</td></tr>";
									$ctr_updatenoUpdates_active++;
									//$notParadox_act.="<tr style='height:35px;'><td colspan='3'>".$arrGetEmpLoans_val["empNo"]."</td></tr>";
								}
								else
								{
									//$notParadox_res.="<tr style='height:35px;'><td>".$ctr_updatenoUpdates_res.". ".$arrGetEmpLoans_val["empNo"]."  ".$arrGetEmpLoans_val["empLastName"].", ".$arrGetEmpLoans_val["empFirstName"]."</td><td>".$arrGetEmpLoans_val["lonTypeDesc"]."</td><td>".$arrGetEmpLoans_val["lonTypeCd"]."</td><td>".$arrchkLoans_reason["loanTypeCode"]."</td><td>NO UPDATES TO BE MADE</td></tr>";
									//$ctr_updatenoUpdates_res++;
									//$notParadox_res.=$arrGetEmpLoans_val["empNo"]."<br>";
									
								}
							}
							
						}
						
				}
				echo "<br><table border='1' width='100%' style='border-collapse:collapse'>";
						echo "<tr style='height:35px;'>";
							echo "<td>Employee No.</td>";
							echo "<td>Loan Type</td>";
							echo "<td>Current HRIS Loan Set Up</td>";
							echo "<td>Current Paradox Loan Set Up</td>";
							echo "<td>Updated To</td>";
						echo "</tr>";
						
						//echo "<br>".$editedupdateLoanPrin.$editedupdateLoanPrin_noupdates.$editedRefNo.$editeddateGranted.$disLoanType.$notParadox_act;
						echo $qryUpdateLoanAmt;
				echo "</table>";
				
				echo $ctr;
				//echo $editedRefNo."<br><br>".$editeddateGranted."<br><br>".$disLoanType."<br><br>".$notParadox_act."<br><br>".$notParadox_res."<br>";
			break;
			
			case "CHCKLEGRESTDAY":
				$tsDate = "06/12/2011";
				
				$qryTimesheet = "SELECT     *
								FROM         tblTimeSheet
								WHERE     (empNo IN
										  (SELECT     empNo
											FROM          tblEmpmast
											WHERE      empStat IN ('RG', 'PR', 'CN') 
											AND empPayGrp = '".$_SESSION["pay_group"]."' AND 
											empPayCat = '3' and empPayType in ('M','D')) ) AND (tsDate = '".$tsDate."') AND (dayType = '05')";
				$resTimesheet = $scriptsObj->execQry($qryTimesheet);
				$arrTimesheet = $scriptsObj->getArrRes($resTimesheet);	
				
				$ctr = 0;
				foreach($arrTimesheet as $arrTimesheet_val)
				{
					$prevtsDate = date("m/d/Y", mktime (0,0,0,date("m", strtotime($tsDate)),date("d", strtotime($tsDate))-1,  date("Y", strtotime($tsDate))));
					
					//Check Previous Timesheet
					//Regular Day
					$arrprev_ts_regDay = getTimesheet($arrTimesheet_val["empNo"], $prevtsDate, "01", " and hrsAbsent='8'");
					
					if($arrprev_ts_regDay["empNo"]!="")
					{
						//echo "Absent Previous = ".$arrprev_ts_regDay["empNo"]."=".$prevtsDate."<br>";
						//echo $arrprev_ts_regDay["empNo"]."<br>";
						
						 $queryCheckEarningsHist = "Select * from tblEarnings where empNo='".$arrprev_ts_regDay["empNo"]."' and trnCode ='0410';";	
						$resCheckEarningsHist = $scriptsObj->execQry($queryCheckEarningsHist);
						$arrCheckEarningsHist = $scriptsObj->getSqlAssoc($resCheckEarningsHist);
						//echo $queryCheckEarningsHist."<br>";
						if($arrCheckEarningsHist["empNo"]!="")
						{	
							echo $arrCheckEarningsHist["empNo"].",<br>";
							//echo $arrprev_ts_regDay["empNo"]."=".$prevtsDate .",<br>";
							//echo $arrprev_ts_regDay["empNo"]."= -".$arrCheckEarningsHist["trnAmountE"]."<br>";
							//echo "Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount, payGrp, payCat, earnStat, trnTaxCd) 
								 // values('".$_SESSION["company_code"]."','0401-04-17-2011(LEGAL HOL ADJ.)','".$arrCheckEarningsHist["empNo"]."','".$ctr."','0804','-".$arrCheckEarningsHist["trnAmountE"]."','".$_SESSION["pay_group"]."', '3','A','Y');<br>";
						}
						
		
					}

					
					
					//RestDay
					$arrprev_ts_rd = getTimesheet($arrTimesheet_val["empNo"], $prevtsDate, "02", " and hrsOtLe8='0'");
					if($arrprev_ts_rd["empNo"]!="")
					{
						$prevtsDate2 = date("m/d/Y", mktime (0,0,0,date("m", strtotime($prevtsDate)),date("d", strtotime($prevtsDate))-1,  date("Y", strtotime($prevtsDate))));
						
						$arrprev_ts_regDay = getTimesheet($arrTimesheet_val["empNo"], $prevtsDate2, "01", " and hrsAbsent='8'");
						
						if($arrprev_ts_regDay["empNo"]!="")
							echo "Absent Previous = ".$arrprev_ts_regDay["empNo"]."=".$prevtsDate2."<br>";
						
						$arrprev_ts_rd = getTimesheet($arrTimesheet_val["empNo"], $prevtsDate2, "02", " and hrsOtLe8='0'");
						
						
						//RestDay
						if($arrprev_ts_rd["empNo"]!="")
						{
							$prevtsDate3 = date("m/d/Y", mktime (0,0,0,date("m", strtotime($prevtsDate2)),date("d", strtotime($prevtsDate2))-1,  date("Y", strtotime($prevtsDate2))));
						
							$arrprev_ts_regDay = getTimesheet($arrTimesheet_val["empNo"], $prevtsDate3, "01", " and hrsAbsent='8'");
							
							if($arrprev_ts_regDay["empNo"]!="")
								echo "Absent Previous = ".$arrprev_ts_regDay["empNo"]."=".$prevtsDate3."<br>";
							
							$arrprev_ts_rd = getTimesheet($arrTimesheet_val["empNo"], $prevtsDate3, "02", " and hrsOtLe8='0'");
						}
							
					}
					$ctr++;
				}
			break;
			
			case "COMPARADOXHRIS":
				$qryParadox = "Select * from EXCEL_PARADOX";
				$resParadox = $scriptsObj->execQry($qryParadox);
				$arrParadox = $scriptsObj->getArrRes($resParadox);	
				
				echo "<table border='1'>";
				$ctr = 1;
				foreach($arrParadox as $arrParadox_val)
				{
					echo "<tr>";
						
						$empFirstName = substr($arrParadox_val["EmpFirstName"], 0, strlen($arrParadox_val["EmpFirstName"])-2);
						
						/*echo "<td>".$ctr." ".$arrParadox_val["EmpLastName"]."</td>";
						echo "<td>".$arrParadox_val["EmpFirstName"]."</td>";
						echo "<td>".$arrParadox_val["LoanAmount"]."</td>";*/
						
						$qryHris = "Select * from EXCEL_HRIS where EmpLastName='".$arrParadox_val["EmpLastName"]."' and EmpFirstName='".$empFirstName."'";
						$reschkHris = $scriptsObj->execQry($qryHris);
						$arrchkHris= $scriptsObj->getSqlAssoc($reschkHris);
						
						$empFirstName = substr($arrParadox_val["EmpFirstName"], 0, strlen($arrParadox_val["EmpFirstName"])-2);
						
						
						
						if($arrchkHris["EmpLastName"]=="")
						{
							$qryHris = "Select * from EXCEL_HRIS where EmpFirstName='".$empFirstName."';";
							$reschkHris = $scriptsObj->execQry($qryHris);
							$num_chkHris= $scriptsObj->getRecCount($reschkHris);
							$arrchkHris= $scriptsObj->getSqlAssoc($reschkHris);
							if($num_chkHris>1)
							{
								$empMidName_initial = substr($arrParadox_val["EmpFirstName"], -1);
								
								$qryHris = "Select * from EXCEL_HRIS where EmpFirstName='".$empFirstName."' and EmpMidName='".$empMidName_initial."';";
								$reschkHris = $scriptsObj->execQry($qryHris);
								$num_chkHris= $scriptsObj->getRecCount($reschkHris);
								$arrchkHris= $scriptsObj->getSqlAssoc($reschkHris);
								
								if($num_chkHris==0)
								{
									$empMidName_initial = $arrParadox_val["EmpLastName"][0];
									
									$qryHris = "Select * from EXCEL_HRIS where EmpFirstName='".$empFirstName."' and EmpSssNo='".$arrParadox_val["EmpSssNo"]."';";
									$reschkHris = $scriptsObj->execQry($qryHris);
									$num_chkHris= $scriptsObj->getRecCount($reschkHris);
									$arrchkHris= $scriptsObj->getSqlAssoc($reschkHris);
									
									echo "<td>HELLO".$arrchkHris["EmpLastName"]."</td>";
									if($arrParadox_val["LoanAmount"]==$arrchkHris["LoanAmount"])
										echo "<td>"."TRUE = ".$arrParadox_val["LoanAmount"]."=".$arrchkHris["LoanAmount"]."</td>";
								}
								else
								{
									echo "<td>".$arrchkHris["EmpLastName"]."</td>";
									if($arrParadox_val["LoanAmount"]==$arrchkHris["LoanAmount"])
										echo "<td>"."TRUE = ".$arrParadox_val["LoanAmount"]."=".$arrchkHris["LoanAmount"]."</td>";
								}
							}
							else
							{
								echo "<td>".$arrchkHris["EmpLastName"]."</td>";
								if($arrParadox_val["LoanAmount"]==$arrchkHris["LoanAmount"])
										echo "<td>"."TRUE = ".$arrParadox_val["LoanAmount"]."=".$arrchkHris["LoanAmount"]."</td>";
							}
						}
						else
						{
							//echo "<td>".$arrchkHris["EmpLastName"]."</td>";
							if($arrParadox_val["LoanAmount"]!=$arrchkHris["LoanAmount"])
							{
								$empLoanAmount_Paradox = $arrParadox_val["LoanAmount"];
								$empLoanAmount_initial = substr($arrParadox_val["LoanAmount"], 0, 1); 
								
								if($empLoanAmount_initial!=0)
									$empLoanAmt_HRIS = $arrParadox_val["LoanAmount"];
								else
									$empLoanAmt_HRIS = $arrchkHris["LoanAmount"];
								
								//	if($arrParadox_val["LoanAmount"]!=$empLoanAmt_HRIS)
									//echo "<td>".$arrchkHris["EmpLastName"]."</td>";
									
									$updateLoan = "Update EXCEL_HRIS set LoanAmount = '".$empLoanAmt_HRIS."'
													where EmpLastName='".$arrParadox_val["EmpLastName"]."' and EmpFirstName='".$empFirstName."'
													and EmpLoanGranted='".$arrParadox_val["EmpLoanGranted"]."';";
									
									echo $updateLoan."<br>";
										//echo "<td>".$updateLoan."</td>";
									
									//else
										//echo "<td>"."TRUE = ".$arrParadox_val["LoanAmount"]."=".$arrchkHris["LoanAmount"]."</td>";
							}
							//else
										//echo "<td>"."TRUE = ".$arrParadox_val["LoanAmount"]."=".$arrchkHris["LoanAmount"]."</td>";
							
							
						}
					echo "</tr>";
					/*echo "<tr>";
						echo "<td>".$ctr." ".$arrParadox_val["EmpLastName"]."</td>";
						$empFirstName = substr($arrParadox_val["EmpFirstName"], 0, strlen($arrParadox_val["EmpFirstName"])-2);
						echo "<td>".$empFirstName."</td>";
						$empLoanAmount_Paradox = $arrParadox_val["LoanAmount"];
						$empLoanAmount_initial = substr($arrParadox_val["LoanAmount"], 0, 1); 
						
						if($empLoanAmount_initial!=0)
							echo "<td>".$arrParadox_val["LoanAmount"]."</td>";
						else
							echo "<td>".substr($arrParadox_val["LoanAmount"], 1, strlen($arrParadox_val["LoanAmount"]))."</td>";
						
						$qryHris = "Select * from EXCEL_HRIS where EmpLastName='".$arrParadox_val["EmpLastName"]."' and EmpFirstName='".$empFirstName."'";
						$reschkHris = $scriptsObj->execQry($qryHris);
						$arrchkHris= $scriptsObj->getSqlAssoc($reschkHris);
						$empLoanAmount_Hris = $arrchkHris["LoanAmount"];
						
						if($arrchkHris["EmpLastName"]!="")
						{
							//echo "<td>".$qryHris."</td>";
							echo "<td>".$arrchkHris["EmpLastName"]."</td>";
							echo "<td>".$arrchkHris["EmpFirstName"]."</td>";
							echo "<td>".$arrchkHris["LoanAmount"]."</td>";
							
							if($empLoanAmount_Paradox==$empLoanAmount_Hris )
								echo "<td>TRUE</td>";
							else 
							{
								$empLoanAmount_Paradox = substr($arrParadox_val["LoanAmount"], 1, strlen($arrParadox_val["LoanAmount"]))."0";
								if($empLoanAmount_Paradox==$empLoanAmount_Hris)
									echo "<td>TRUE-EDITED</td>";
							}
						}
						else
						
						
				
					echo "</tr>";*/
					$ctr++;
				}
				echo "</table>";
			break;
			
			case "UPDATETBLEMPLOANS":
				$qryUpdate = "Select * from PPCI_LOAN_HIST_06182011";
				$resUpdate = $scriptsObj->execQry($qryUpdate);
				$arrUpdate = $scriptsObj->getArrRes($resUpdate);	
				
				foreach($arrUpdate as $arrUpdate_val)
				{
					echo "Update tblEmpLoans set lonAmt='".$arrUpdate_val["LoanAmount_Update"]."' where empNo='".$arrUpdate_val["empNo"]."' and LonTypeCd like '1%';<br>";
					//echo "Select empNo, lonAmt, lo_old from tblEmpLoans where empNo='".$arrUpdate_val["empNo"]."' and LonTypeCd like '1%';<br>";
					//echo $arrUpdate_val["empNo"].",";
				}
			break;
		}
		
		
	
	}
	
	function getTimesheet($empNo, $prevDate, $dayType, $where)
	{
		extract($GLOBALS);
		
		
		if($dayType=="02")
		{	
		 	$qryprev_Timesheet_regDay = "SELECT     *
					FROM         tblTimeSheet
					WHERE     empNo='".$empNo."' AND (tsDate = '".$prevDate."') AND (dayType = '".$dayType."') $where;<br>";
		}
		
		$qryprev_Timesheet_regDay = "SELECT     *
					FROM         tblTimeSheet
					WHERE     empNo='".$empNo."' AND (tsDate = '".$prevDate."') AND (dayType = '".$dayType."') $where;";
		$resprev_ts_regDay = $scriptsObj->execQry($qryprev_Timesheet_regDay);
		return $scriptsObj->getSqlAssoc($resprev_ts_regDay);
		
		
		
	}
	
	function getPrevTsData($empNo, $prev_tsDate, $dayType = "")
	{
		extract($GLOBALS);
		if($dayType!="")
			$where = " and dayType='".$dayType."'";
			
		$qryTsPrevDay = "SELECT * FROM tblTimeSheet
				  WHERE compCode = '".$_SESSION["company_code"]."'
				  AND empNo = '".$empNo."' 
				  AND tsDate ='".date('m/d/Y', $prev_tsDate)."' $where; ";
		$resTsPrevDay = $scriptsObj->execQry($qryTsPrevDay);
		return $scriptsObj->getSqlAssoc($resTsPrevDay);
	}
	
	
?>