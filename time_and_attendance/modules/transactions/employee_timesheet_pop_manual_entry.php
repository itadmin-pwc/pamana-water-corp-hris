<?
/*
	Date Created	:	08042010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

include("transaction_obj.php");

$emptimesheetObj = new transactionObj();
$sessionVars = $emptimesheetObj->getSeesionVars();
$emptimesheetObj->validateSessions('','MODULES');

$arr_empInfo = $emptimesheetObj->getEmployee($_SESSION["company_code"],$_GET["empNo"],'');
$arr_EmpTsInfo =  $emptimesheetObj->getTblData("tblTK_Timesheet", " and empNo='".$arr_empInfo["empNo"]."' and tsDate = '".date("Y-m-d", strtotime($_GET["tsDate"]))."'", " ", "sqlAssoc");


	
switch($_GET["action"])
{
	
	case "Update":
		$btnAction = "Update";
		$hdrTitle = "Actual Employee Schedule";
		$arr_EmpTsCorrInfo =  $emptimesheetObj->getTblData("tblTK_Timesheet", " and empNo='".$arr_empInfo["empNo"]."' and tsDate = '".date("Y-m-d", strtotime($_GET["tsDate"]))."'", " ", "sqlAssoc");
		$lunchOut = $arr_EmpTsCorrInfo["lunchOut"];
		$lunchIn = $arr_EmpTsCorrInfo["lunchIn"];

	break;
	
	case "Update_TsCorr":
		$btnAction = "Update Correction";
		$hdrTitle = "Timesheet Correction";
		$arr_EmpTsCorrInfo =  $emptimesheetObj->getTblData("tblTK_TimesheetCorr", " and empNo='".$arr_empInfo["empNo"]."' and tsDate = '".date("Y-m-d", strtotime($_GET["tsDate"]))."'", " ", "sqlAssoc");
		$lunchOut = $arr_EmpTsCorrInfo["lunchOut"];
		$lunchIn = $arr_EmpTsCorrInfo["lunchIn"];
	break;
	
	case "getShiftCodeDtl":
		
		/*if($_GET["violationCd"]=='09')
		{
			echo "$('txtElunchIn').value='".$arr_EmpTsInfo["shftLunchIn"]."';";
			echo "$('txtElunchOut').value='".$arr_EmpTsInfo["shftLunchOut"]."';";
		}*/
	
		exit();
	break;
		
}
echo $hdrTitle;

$DayTypeDesc = $emptimesheetObj->getDayTypeDescArt($arr_EmpTsInfo["dayType"]);
$appTypeDesc = $emptimesheetObj->getTblData("tblTK_AppTypes", " and tsAppTypeCd='".$arr_EmpTsInfo["tsAppTypeCd"]."'", "", "sqlAssoc");
$arr_ViolationDesc = $emptimesheetObj->makeArr($emptimesheetObj->getTblData("tblTK_ViolationType", "", " order by violationDesc", ""), 'violationCd','violationDesc','');
$arr_OpenPeriod = $emptimesheetObj->makeArr($emptimesheetObj->getPeriodWil($_SESSION["company_code"],$arr_empInfo["empPayGrp"],$arr_empInfo["empPayCat"]," "),'pdSeries','pdPayable','');
$arr_EmpShift = $emptimesheetObj->getTblData("tblTk_EmpShift", " and empNo='".$arr_empInfo["empNo"]."'", "", "sqlAssoc");

//if the Shift Schedule != Actual Schedule mark it as Red
$font_actualTs = "0000FF";
$font_editedTs = "993300";



?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
        <script type="text/javascript" src="../../../includes/calendar.js"></script>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
    	
		<FORM name="frmEmpTimeSheet" id="frmEmpTimeSheet" action="<?=$_SERVER['PHP_SELF']?>" method="post">
		<input type="hidden" name="action" value="<?=$_GET["action"]?>">
        <input type="hidden" name="txtcrossDay" value="<?=(($arr_EmpTsInfo["crossDay"]=='Y')||($arr_EmpTsInfo["otCrossTag"]=='Y')?"Y":"")?>">	
        <input type="hidden" name="txtAppType" value="<?=$arr_EmpTsInfo["tsAppTypeCd"]?>">	
        <input type="hidden" name="txtempBranchCode" value="<?=$arr_empInfo["empBrnCode"]?>">
        <input type="hidden" name="txtUtExempt" value="<?=$arr_EmpShift["utHrsExempt"]?>">
        <input type="hidden" name="txtDayTypeCd" value="<?=$arr_EmpTsInfo["dayType"]?>">
        <input type="hidden" name="empNo" value="<?=$arr_empInfo["empNo"]?>">
            <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
               
                <tr style="height:30px;">
                    <td align='center' colspan='6' class='prevEmpHeader'>
                        <?="TIMESHEET OF EMPLOYEE : ".$arr_empInfo["empNo"]." - ".$arr_empInfo["empLastName"].", ".$arr_empInfo["empFirstName"]." ".$arr_empInfo["empMidName"][0]."."?>
                    </td>  
                </tr> 
                
                <tr>
                    <td width='20%' class='gridDtlLbl' align='left'>Time Sheet Date</td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='80%' class='gridDtlVal'>
                        <input type='text' class='inputs' name='txttsDate' id='txttsDate' style='width:40%;' readonly value='<?=$_GET["tsDate"]?>' >
                         <input type='text' class='inputs'  style='width:30%;' readonly value='<?=((($arr_EmpTsInfo["crossDay"]=='Y')||($arr_EmpTsInfo["otCrossTag"]=='Y'))?" - (CROSS DAY)":"")?>' >
                    </td>
                </tr>
                
                <tr>
                    <td width='20%' class='gridDtlLbl' align='left'>Day Type </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='80%' class='gridDtlVal'>
                        <input type='text' class='inputs' name='txtDayType' id='txtDayType' style='width:40%;' readonly value='<?=$DayTypeDesc?>' ><br>
                    </td>
                </tr>
                
               <tr>
                	<td colspan="3">&nbsp;</td>
                </tr>
                
                <tr>
                	<td class='hdrLblRow' colspan="3"><font class="hdrLbl">Employee Shift Schedule</font></td>
                </tr>
                
                <tr>
                	<td colspan="3">
                    	<TABLE border="1" width="100%" cellpadding="1" cellspacing="1" style="border-collapse:collapse;">
                        	<tr  class="gridToolbar" align="center" style="height:25px;">
                            	<td width="8.33%" class="gridDtlLbl" >Time<br />In</td>
                                <td width="8.33%" class="gridDtlLbl" >Lunch<br />Out</td>
                                <td width="8.33%" class="gridDtlLbl" >Lunch<br />In</td>
                                <td width="8.33%" class="gridDtlLbl" >Brk.<br />Out</td>
                                <td width="8.33%" class="gridDtlLbl" >Brk.<br />In</td>
                                <td width="8.33%" class="gridDtlLbl" >Time<br />Out</td>
                                <td width="16.66%" class="gridDtlLbl" >Remarks</td>
                               
                            </tr>
                            
                            <tr  class="gridToolbar" align="center" style="height:25px;">
                            	<input type="hidden" name="shiftSched_Tin" id="shiftSched_Tin" value="<?=$arr_EmpTsInfo["shftTimeIn"]?>">
                                <input type="hidden" name="shiftSched_Lout" id="shiftSched_Lout" value="<?=$arr_EmpTsInfo["shftLunchOut"]?>">
                                <input type="hidden" name="shiftSched_Lin" id="shiftSched_Lin" value="<?=$arr_EmpTsInfo["shftLunchIn"]?>">
                                <input type="hidden" name="shiftSched_Bout" id="shiftSched_Bout" value="<?=$arr_EmpTsInfo["shftBreakOut"]?>">
                                <input type="hidden" name="shiftSched_Bin" id="shiftSched_Bin" value="<?=$arr_EmpTsInfo["shftBreakIn"]?>">
                                <input type="hidden" name="shiftSched_Tout" id="shiftSched_Tout" value="<?=$arr_EmpTsInfo["shftTimeOut"]?>">
                                <input type="hidden" name="txtlogsExceeded" id="txtlogsExceeded" value="<?=$arr_EmpTsInfo["logsExceeded"]?>">
                                
                                <td width="8.33%" class='gridDtlVal' align="center"><?=$arr_EmpTsInfo["shftTimeIn"]?></td>
                                <td width="8.33%" class='gridDtlVal' align="center"><?=$arr_EmpTsInfo["shftLunchOut"]?></td>
                                <td width="8.33%" class='gridDtlVal' align="center"><?=$arr_EmpTsInfo["shftLunchIn"]?></td>
                                <td width="8.33%" class='gridDtlVal' align="center"><?=$arr_EmpTsInfo["shftBreakOut"]?></td>
                                <td width="8.33%" class='gridDtlVal' align="center"><?=$arr_EmpTsInfo["shftBreakIn"]?></td>
                                <td width="8.33%" class='gridDtlVal' align="center"><?=$arr_EmpTsInfo["shftTimeOut"]?></td>
                           		<td width="16.66%" class='gridDtlVal' align="center"><?=$appTypeDesc["appTypeDesc"]?></td>
                            </tr>
                        </TABLE>
                  	</td>
                </tr>
                
                <tr>
                	<td colspan="3">&nbsp;</td>
                </tr>
                
                <tr>
                	<td class='hdrLblRow' colspan="3"><font class="hdrLbl"><?=$hdrTitle?></font></td>
                </tr>
                
                <tr>
                	<td colspan="3">
                    	<TABLE border="1" width="100%" cellpadding="1" cellspacing="1" style="border-collapse:collapse;">
                        	<tr  class="gridToolbar" align="center" style="height:25px;">
                            	<td width="8.33%" class="gridDtlLbl" >Time<br />In</td>
                                <td width="8.33%" class="gridDtlLbl" >Lunch<br />Out</td>
                                <td width="8.33%" class="gridDtlLbl" >Lunch<br />In</td>
                                <td width="8.33%" class="gridDtlLbl" >Brk.<br />Out</td>
                                <td width="8.33%" class="gridDtlLbl" >Brk.<br />In</td>
                                <td width="8.33%" class="gridDtlLbl" >Time<br />Out</td>
                                <td width="5%" class="gridDtlLbl" >Cross<br />Day</td>
                               
                                <td width="11.66%" class="gridDtlLbl" >Violation Type</td>
                                
                               
                            </tr>
                            
                            <tr  class="gridToolbar" align="center" style="height:25px;">
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs'   style='width:50%;  color:<?=$font_actualTs?>;' readonly value='<?=$arr_EmpTsCorrInfo["timeIn"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs'  style='width:50%;  color:<?=$font_actualTs?>;' readonly value='<?=$arr_EmpTsCorrInfo["lunchOut"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs'  style='width:50%; color:<?=$font_actualTs?>;' readonly value='<?=$arr_EmpTsCorrInfo["lunchIn"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs'  style='width:50%; color:<?=$font_actualTs?>;' readonly value='<?=$arr_EmpTsCorrInfo["breakOut"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs'  style='width:50%; color:<?=$font_actualTs?>;' readonly value='<?=$arr_EmpTsCorrInfo["breakIn"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs'  style='width:50%; color:<?=$font_actualTs?>;' readonly value='<?=$arr_EmpTsCorrInfo["timeOut"]?>'></td>
                           		<td width="5%" align="center" class='gridDtlVal'><input type='checkbox' class='inputs' disabled <?=($arr_EmpTsCorrInfo["crossTag"]=='Y'?"checked":"")?>></td>
                           		
                                <td width="11.66%" align="center" class='gridDtlVal'><?php $emptimesheetObj->DropDownMenu($arr_ViolationDesc,'violationCd',$arr_EmpTsCorrInfo["editReason"], ' style="width:100%;" class="gridDtlVal" disabled'); ?></td>
                           	</tr>
                            
                             <tr  class="gridToolbar" align="center" style="height:25px;">
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs' name='txtEtimeIn' id='txtEtimeIn'  style='width:50%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsCorrInfo["timeIn"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs' name='txtElunchOut' id='txtElunchOut'  style='width:50%;  color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$lunchOut?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs' name='txtElunchIn' id='txtElunchIn' style='width:50%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$lunchIn?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs' name='txtEbrkOut' id='txtEbrkOut' style='width:50%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsCorrInfo["breakOut"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs' name='txtEbrkIn' id='txtEbrkIn' style='width:50%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsCorrInfo["breakIn"]?>'></td>
                                <td width="8.33%" align="center" class='gridDtlVal'><input type='text' class='inputs' name='txtEtimeOut' id='txtEtimeOut' style='width:50%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsCorrInfo["timeOut"]?>'></td>
                           		<td width="5%" align="center" class='gridDtlVal'><input type='checkbox' class='inputs'  name='txtCheckTag' id='txtCheckTag'  <?=($arr_EmpTsCorrInfo["crossTag"]=='Y'?"checked":"")?>></td>
                                <td width="11.66%" align="center" class='gridDtlVal'><?php $emptimesheetObj->DropDownMenu($arr_ViolationDesc,'violationCd',$arr_EmpTsCorrInfo["editReason"], ' style="width:100%;" class="gridDtlVal" onChange=getShiftCodeDetail(this.value);'); ?></td>
                            </tr>
                        </TABLE>
                  	</td>
                </tr>
                
                
                <tr>
                	<td colspan="6"  class='childGridFooter' align="center">
                    	<input type='button' class= 'inputs' name='btnReset' value='Reset' onClick="reset_tsCorr();">
                    	<input type='button' class= 'inputs' name='btnCopySched' value='Copy Original Schedule' onClick="copySchedule();">
                        <input type='button' class= 'inputs' name='btnUserDef' value='<?=$btnAction?>' onClick="chkBreak(<?php echo "'".$btnAction."'";?>);">
                    </td>
                </tr>
            </TABLE>
			
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function chkBreak(btnAction)
	{
		var frmSer_a = $('frmEmpTimeSheet').serialize(true);

		if((frmSer_a['violationCd']=="0"))
		{
			alert('Violation Type is required.');
			$('violationCd').focus();
			return false;
		}

		if((frmSer_a['txtEbrkOut']!="") && (frmSer_a['txtEbrkOut']!=":"))
		{
			if((frmSer_a['txtEbrkIn']=="") || (frmSer_a['txtEbrkIn']==":"))
			{
				alert('Break In is required.');
				$('txtEbrkIn').focus();
				return false;
			}
			else
				saveEmpTimeSheet(btnAction);
		}
		else if((frmSer_a['txtEbrkIn']!="") && (frmSer_a['txtEbrkIn']!=":"))
		{
			if((frmSer_a['txtEbrkOut']=="") || (frmSer_a['txtEbrkOut']==":"))
			{
				alert('Break Out is required.');
				$('txtEbrkOut').focus();
				return false;
			}
			else
				saveEmpTimeSheet(btnAction);
		}
		else
		{
			saveEmpTimeSheet(btnAction);
		}
	}
	
	function saveEmpTimeSheet(btnAction)
	{
		if(btnAction=='Update')
		{
			params = 'employee_timesheet_manual_entry.php';
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmEmpTimeSheet').serialize(),
				onComplete : function (req){
					eval(req.responseText);
				}
			});
		}
		else
		{
			var confirmUser = confirm("Are you sure you want to Update the TimeSheet Correction?");
		
			if(confirmUser==true)
			{
				params = 'employee_timesheet_manual_entry.php?action=UpdateCorr';
				new Ajax.Request(params,{
					method : 'get',
					parameters : $('frmEmpTimeSheet').serialize(),
					onComplete : function (req){
						eval(req.responseText);
					}	
				});
			}
		}
	}
	
	
	function copySchedule()
	{
		var frmSer_a = $('frmEmpTimeSheet').serialize();
		var frmSer_c = $('frmEmpTimeSheet').serialize(true);
		var tsCorrFields = new Array('txtEtimeIn','txtElunchOut','txtElunchIn','txtEbrkOut','txtEbrkIn','txtEtimeOut');
		frmSer_b = frmSer_a.split('&');
		
		tsCorr = 0;
		for(i=9;i<parseInt(frmSer_b.length)-10;i++){
			frmSer_d = frmSer_b[i].split("=");
			document.frmEmpTimeSheet[tsCorrFields[tsCorr]].value = document.frmEmpTimeSheet[frmSer_d[0]].value;
			tsCorr++;
		}
	}
	
	function reset_tsCorr()
	{
		var frmSer_a = $('frmEmpTimeSheet').serialize();
		var frmSer_c = $('frmEmpTimeSheet').serialize(true);
		var tsCorrFields = new Array('txtEtimeIn','txtElunchOut','txtElunchIn','txtEbrkOut','txtEbrkIn','txtEtimeOut');
		frmSer_b = frmSer_a.split('&');
		
		tsCorr = 0;
		for(i=9;i<parseInt(frmSer_b.length)-10;i++){
			frmSer_d = frmSer_b[i].split("=");
			document.frmEmpTimeSheet[tsCorrFields[tsCorr]].value = '';
			tsCorr++;
		}
	}
	
	function getShiftCodeDetail(test)
	{
		var empNo = document.frmEmpTimeSheet.empNo.value;
		var tsDate = document.frmEmpTimeSheet.txttsDate.value;
		var violationCd = document.frmEmpTimeSheet.violationCd.value;
		
		params = 'employee_timesheet_pop_manual_entry.php?action=getShiftCodeDtl&empNo='+empNo+'&tsDate='+tsDate+'&violationCd='+test;
				
		new Ajax.Request(params,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}
	
	
</SCRIPT>