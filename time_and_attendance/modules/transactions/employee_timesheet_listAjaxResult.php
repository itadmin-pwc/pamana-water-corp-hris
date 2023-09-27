<?
/*
	Created By 	:	Arong, Genarra Jo-Ann S.
	Date Created:	07/23/2010 Friday
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

include("transaction_obj.php");

$emptimesheetObj = new transactionObj();
$sessionVars = $emptimesheetObj->getSeesionVars();
$emptimesheetObj->validateSessions('','MODULES');

$arr_empInfo = $emptimesheetObj->getAllEmployees($_SESSION["company_code"],$_GET["empNo"],'');//$emptimesheetObj->getUserInfo($_SESSION["company_code"],$_GET["empNo"],'');

$arr_Branch = $emptimesheetObj->getEmpBranchArt($_SESSION["company_code"], $arr_empInfo["empBrnCode"]);

$arr_OpenPeriod = $emptimesheetObj->makeArr($emptimesheetObj->getPeriodWil($_SESSION["company_code"],$arr_empInfo["empPayGrp"],$arr_empInfo["empPayCat"]," "),'pdSeries','pdPayable','');



if($_GET["pdSeries"]=="")
{
	
	$pdStat = " and pdTSStat='O'";
	$period_Info = $emptimesheetObj->getPeriodWil($_SESSION["company_code"],$arr_empInfo["empPayGrp"],$arr_empInfo["empPayCat"]," ".$pdStat."");
	$pdSeries = $period_Info["pdSeries"];
}
else
{
	$pdSeries = $_GET["pdSeries"];
}

//echo "PD SERIES = ".$pdSeries."<br> $_GETPDSERIES = ".$_GET["pdSeries"]."="."GENARRA";
$arr_period_Info = $emptimesheetObj->getPeriodWil($_SESSION["company_code"],$arr_empInfo["empPayGrp"],$arr_empInfo["empPayCat"]," and pdSeries='".$pdSeries."'");

	

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Timesheet
			</td>
		</tr>
        
        <tr>
			<td class="parentGridDtl">
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                	<tr class="gridToolbar" style="height:20px;">
                    	<td width="10%"><font class="gridDtlLblTxt">Employee No.</font></td>
                        <td width="1%" align="center">:</td>
                        <td width="25%"><input type='text' class='inputs' name='txtEmpName' id='txtEmpName' style='width:30%;' readonly value='<?=$arr_empInfo["empNo"]?>' ></td>
                        
                        <td width="10%"><font class="gridDtlLblTxt">Branch</font></td>
                        <td width="1%" align="center">:</td>
                        <td width="25%"><input type='text' class='inputs' name='txtEmpName' id='txtEmpName' style='width:70%;' readonly value='<?=$arr_Branch["brnDesc"]?>' ></td>
                    </tr>
                    
                    
                    <tr class="gridToolbar" style="height:20px;">
                    	<td width="10%"><font class="gridDtlLblTxt">Employee Name</font></td>
                        <td width="1%" align="center">:</td>
                        <td width="25%"><input type='text' class='inputs' name='txtEmpName' id='txtEmpName' style='width:70%;' readonly value='<?=$arr_empInfo["empLastName"].", ".$arr_empInfo["empFirstName"]." ".$arr_empInfo["empMidName"][0]."."?>' ></td>
                        
                        <td width="10%"><font class="gridDtlLblTxt">Payroll Cut - Off</font></td>
                        <td width="1%" align="center">:</td>
                        <td width="25%">
                        	<?php
								$emptimesheetObj->DropDownMenu($arr_OpenPeriod,'pdSeries',$pdSeries,"onChange=\"changePayPeriod();\"");
                            ?>
                            <input name="back" class="inputs" type="button" id="back" value="Back" onClick="location.href='view_edit_employee_timesheet.php?&url=<?=$_GET["url"]?>';">
                        </td>
                    </tr>
                    
                    <tr class="gridToolbar">
                    	<td colspan="6" class="gridToolbar" >
                        <br />
                        	<TABLE border="0" width="100%" >
                            	<tr>
                                	<td width="50%" >
                                    	<div id="Panel1" style="height: 100%; width:100%; overflow-y: scroll;">   
                                    	<TABLE border="1" width="100%" cellpadding="1" cellspacing="1" style="border-collapse:collapse;">
                                        	<tr   class="gridToolbar" align="center" style="height:30px;">
                                            	<td   colspan="2"></td>
                                                <td   colspan="7" class="fntTblHdr" style="font-size:12px;">SHIFT SCHEDULE</td>
                                                <td  colspan="8" class="fntTblHdr" style="font-size:12px;">ACTUAL LOGS</td>
                                                 <td  colspan="3"></td>
                                            </tr>
                                        	<tr  class="gridToolbar" align="center" style="height:25px;">
                                            	<td width="6%" class="fntTblHdr" style="font-size:11px;">TS.<br />Date</td>
                                                <td width="7%" class="fntTblHdr" style="font-size:11px;">Day<br />Type</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Time<br />In</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Lunch<br />Out</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Lunch<br />In</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Brk.<br />Out</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Brk.<br />In</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Time<br />Out</td>
                                                <td width="7%" class="fntTblHdr" style="font-size:11px;">App. Type <br /> Vio. Type</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Time<br />In</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Lunch<br />Out</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Lunch<br />In</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Brk.<br />Out</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Brk.<br />In</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Time<br />Out</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">OT<br />In</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">OT<br />Out</td>
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Hrs.<br />Worked</td>
                                                
                                                <td width="5%" class="fntTblHdr" style="font-size:11px;">Check Tag</td>
                                            </tr>
                                       </TABLE>
                                      </div>
                                     <div id="Panel1" style="height: 200px; width:1255px; overflow-y: scroll;">   
                                     	<TABLE border="1" width="100%"  cellpadding="1" cellspacing="1" style="border-collapse:collapse;">
                                        	<?php
												$arr_EmpTsInfo =  $emptimesheetObj->getTblData("tblTK_Timesheet", " and empNo='".$arr_empInfo["empNo"]."' and tsDate between '".date("Y-m-d", strtotime($arr_period_Info["pdFrmDate"]))."' and '".date("Y-m-d", strtotime($arr_period_Info["pdToDate"]))."'", " order by tsDate", "");
											
												foreach($arr_EmpTsInfo as $arr_EmpTsInfo_val)
												{
													$f_color = "";
													$DayTypeDesc = $emptimesheetObj->getDayTypeDescArt($arr_EmpTsInfo_val["dayType"]);
													$appTypeDesc = $emptimesheetObj->getTblData("tblTK_AppTypes", " and tsAppTypeCd='".$arr_EmpTsInfo_val["tsAppTypeCd"]."'", "", "sqlAssoc");
													$vioTypeDesc = $emptimesheetObj->getTblData("tblTK_ViolationType", " and violationCd='".$arr_EmpTsInfo_val["editReason"]."'", "", "sqlAssoc");
													
													if($arr_EmpTsInfo_val["dayType"]=='02')
														$f_color = "#CC3300";
													  elseif($arr_EmpTsInfo_val["dayType"]=='03')
													  	$f_color = "#990099";
													  elseif($arr_EmpTsInfo_val["dayType"]=='04')
													  	$f_color = "#993300";
													  elseif($arr_EmpTsInfo_val["dayType"]=='05')
													  	$f_color = "#009900";
													  elseif($arr_EmpTsInfo_val["dayType"]=='06')
													  	$f_color = "#000099";
													  elseif($arr_EmpTsInfo_val["logsExceeded"]=='Y')
													  	$f_color = "#CC0033";
													 
											?>
                                                    <tr  class="gridToolbar" align="center" style="height:25px;">
                                                        <td width="6%" ><font color="<?=$f_color?>"> <?=date("Y-m-d", strtotime($arr_EmpTsInfo_val["tsDate"]))?></font></td>
                                                        <td width="7%" ><font style="font-family : Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold" color="<?=$f_color?>"><?=$DayTypeDesc."<br>".($arr_EmpTsInfo_val["logsExceeded"]=='Y'?"Logs Exceeded":"")?></font></td>
                                                        <td width="5%" ><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["shftTimeIn"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["shftLunchOut"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["shftLunchIn"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["shftBreakOut"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["shftBreakIn"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["shftTimeOut"]?></font></td>
                                                        <td width="7%" align="left"><font style="font-family : Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold" color="<?=$f_color?>"><?=$appTypeDesc["appTypeDesc"]."<br>".strtoupper($vioTypeDesc["violationDesc"])?></font></td>
                                                        
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["timeIn"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["lunchOut"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["lunchIn"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["breakOut"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["breakIn"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["timeOut"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["otIn"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["otOut"]?></font></td>
                                                        <td width="5%" align="center"><font color="<?=$f_color?>"><?=$arr_EmpTsInfo_val["hrsWorked"]?></font></td>
                                                        <td align="center">
                                                        
                                                        <?php
                                                        if(($arr_EmpTsInfo_val["checkTag"]=='Y')||($arr_EmpTsInfo_val["checkTag"]==" ")||($arr_EmpTsInfo_val["checkTag"]=="N"))
                                                        {
                                                        ?>
                                                        	<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Shift Code Detail" onclick="maintShiftCode('Update','<?=date("Y-m-d", strtotime($arr_EmpTsInfo_val["tsDate"]))?>','<?=$arr_EmpTsInfo_val["empNo"]?>','<?=$pdSeries?>','employee_timesheet_pop.php','empTimeSheet',0,'','','txtSrch','cmbSrch')"></a>                                    
                                                        <?php
                                                        }
                                                        elseif($arr_EmpTsInfo_val["checkTag"]=='C') 
                                                        {
                                                        	$ediTedby = $emptimesheetObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']); 
                                                       	 	$title =  "Edited By : ".$ediTedby["empLastName"].", ".$ediTedby["empFirstName"][0].".".$ediTedby["empMidName"][0]."."." on ".date("Y-m-d", strtotime($arr_EmpTsInfo_val["dateEdited"]));
                                                        ?>
                                                        	<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/edit_prev_emp.png" title="<?=$title?>" onclick="maintShiftCode('Update_TsCorr','<?=date("Y-m-d", strtotime($arr_EmpTsInfo_val["tsDate"]))?>','<?=$arr_EmpTsInfo_val["empNo"]?>','<?=$pdSeries?>','employee_timesheet_pop.php','empTimeSheet',0,'','','txtSrch','cmbSrch')"></a>                                    
                                                        
                                                        <?php
                                                        }
														elseif($arr_EmpTsInfo_val["checkTag"]=='P')
														{
															echo "POSTED";
														}
                                                        ?>
                                                        </td>
													</tr>
                                            	<?  }  ?>
                                       </TABLE>  		
                                     </div>    
                                    </td>
                                    
                                </tr>
                            </TABLE>
                        </td>
                    </tr>
                    
                    
                   
                </TABLE>
                
                
            </td>
        </tr>
   </TABLE>
    
    
</div>
<? $emptimesheetObj->disConnect();?>

<script>

</script>