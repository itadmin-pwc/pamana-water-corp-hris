<?
/*
	Date Created	:	072010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$shiftCodeTypeObj = new maintenanceObj();
$sessionVars = $shiftCodeTypeObj->getSeesionVars();
$shiftCodeTypeObj->validateSessions('','MODULES');

$arr_Day = array('1'=>'Monday', '2'=>'Tuesday', '3'=>'Wednesday', '4'=>'Thursday', '5'=>'Friday', '6'=>'Saturday', '7'=>'Sunday');

switch($_GET["modAction"])
{
	//edited by nhomer
	case "Add":
		$getLastShiftCode = $shiftCodeTypeObj->getMaxShiftCode("tblTK_ShiftHdr", "", "");
		$newShiftCode = $getLastShiftCode["shiftCode"] + 1;
		$ShiftCode = ($newShiftCode<10?"0".$newShiftCode:$newShiftCode);
		$btnName = "Save";
		$shiftCodeCrossDay = "N";
		
	break;
	//old code
//	case "Add":
//		$getLastShiftCode = $shiftCodeTypeObj->getShiftInfo("tblTK_ShiftHdr", "", " order by seqNo desc");
//		$newShiftCode = $getLastShiftCode["shiftCode"] + 1;
//		$ShiftCode = ($newShiftCode<10?"0".$newShiftCode:$newShiftCode);
//		$btnName = "Save";
//		$shiftCodeCrossDay = "N";
//		
//	break;
	
	case "Edit":
		$arr_ShiftCode_Hdr = $shiftCodeTypeObj->getShiftInfo("tblTK_ShiftHdr", " and shiftCode='".$_GET["shiftCode"]."'", " order by shiftCode desc");
		$ShiftCode = $arr_ShiftCode_Hdr["shiftCode"];
		$shiftCodeDescr = $arr_ShiftCode_Hdr["shiftDesc"];
		$shiftCodeLongDescr = $arr_ShiftCode_Hdr["shiftLongDesc"];
		$btnName = "Update";
		$shiftCodeCrossDay = $arr_ShiftCode_Hdr["crossDay"];
		
	break;
	
	case "crossDayConfirm_Add":
		if($shiftCodeTypeObj->maint_Shift_Code("Add",$_GET))
			echo "alert('Shift Code Detail Successfully Added.');";
		else
			echo "alert('Error in Adding the Shift Code Detail.');";
		exit();
	break;
	
	case "crossDayConfirm_Update":
		if($shiftCodeTypeObj->maint_Shift_Code("Update",$_GET))
			echo "alert('Shift Code Detail Successfully Updated.');";
		else
			echo "alert('Error in Updating the Shift Code Detail.');";
		exit();
	break;
}


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
    	
		<FORM name="frmShiftCode" id="frmShiftCode" action="<?=$_SERVER['PHP_SELF']?>" method="post">
				
            <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
               
                <tr>
                    <td align='center' colspan='6' class='prevEmpHeader'>
                        Shift Code Details
                    </td>  
                </tr> 
                
                <tr>
                    <td width='25%' class='gridDtlLbl' align='left'>Shift Code </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                        <input type='text' class='inputs' name='txtShiftCode' id='txtShiftCode' style='width:20%;' readonly value=<?=$ShiftCode?> >
                        <input type='text' class='inputs' name='txtShiftDesc' id='txtShiftDesc' style='width:77%;' value='<?=$shiftCodeDescr?>' maxlength="20">
                        <input type="hidden" name="action" id="action" value="<?=$btnName?>">
                        <input type="hidden" name="txtDateToday" id="txtDateToday" value="<?=date("m/d/Y")?>">
                    </td>
                    
                    <td width='25%' class='gridDtlLbl' align='left'>Status </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                        <?php 
                            $shiftCodeTypeObj->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbShitCodeStat',$cmbShitCodeStat,'class="inputs" style="width:145px;"');
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <td width='25%' class='gridDtlLbl' align='left'>Shift Long Desc. </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal' colspan="3">
                        <input type='text' class='inputs' name='txtShiftLongDesc' id='txtShiftLongDesc' style='width:100%;' value='<?=$shiftCodeLongDescr?>' maxlength="50">
                    </td>
              		<!--
              		<td width='25%' class='gridDtlLbl' align='left'>Cross Day </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                        <input type="radio" name="crossDay" id="crossDay" value="1" <?php echo ($shiftCodeCrossDay=="Y"?"checked":""); ?>><b>Yes</b>
                        <input type="radio" name="crossDay" id="crossDay" value="0" <?php echo ($shiftCodeCrossDay=="N"?"checked":""); ?>><b>No</b>
                        
                    </td>
                    -->
                </tr>
                
                <tr>
                	<td colspan="6">
                    	<br>
                    	<table border="1" width="100%" style="border-collapse:collapse;">
                        	<tr align="center" class="gridDtlLbl">
                            	<td width="10%" colspan="2"></td>
                                <td width="10%" colspan="2">LUNCH</td>
                                <td width="10%" colspan="2">BREAK</td>
                                <td width="10%" colspan="3"></td>
                            </tr>
                        	<tr align="center" class="gridDtlLbl">
                            	<td width="10%">Day</td>
                                <td width="10%">Time - In</td>
                                <td width="10%">OUT</td>
                                <td width="10%">IN</td>
                            	<td width="10%">OUT</td>
                                <td width="10%">IN</td>
                                <td width="10%">Time - Out</td>
                                <td width="5%">Cross Day</td>
                                <td width="5%">Rest Day</td>
                            </tr>
                            <?php
								foreach($arr_Day as $arr_Day_val=>$dayDesc)
								{
									echo "<tr>";
									$arr_ShiftCode_Dtl = $shiftCodeTypeObj->getShiftInfo("tblTK_ShiftDtl", " and shftCode='".$ShiftCode."' and dayCode='".$arr_Day_val."'", " order by dayCode");
							?>		
                            
										<td class='gridDtlVal' id='<?=$arr_Day_val?>'><font class='gridDtlLblTxt'><?=$dayDesc?></font></td>
										<td class='gridDtlVal' align='center'> <input type='text' class='inputs' name=<?=$arr_Day_val."-"."t_in"?> id=<?=$arr_Day_val."-"."t_in"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftTimeIn"]?>'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" ></td>
										<td class='gridDtlVal' align='center'> <input type='text' class='inputs' name=<?=$arr_Day_val."-"."l_out"?> id=<?=$arr_Day_val."-"."l_out"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftLunchOut"]?>'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"></td>
										<td class='gridDtlVal' align='center'> <input type='text' class='inputs' name=<?=$arr_Day_val."-"."l_in"?> id=<?=$arr_Day_val."-"."l_in"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftLunchIn"]?>'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"></td>
										<td class='gridDtlVal' align='center'> <input type='text' class='inputs' name=<?=$arr_Day_val."-"."b_out"?> id=<?=$arr_Day_val."-"."b_out"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftBreakOut"]?>'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"></td>
										
                                        <td class='gridDtlVal' align='center'> <input type='text' class='inputs' name=<?=$arr_Day_val."-"."b_in"?> id=<?=$arr_Day_val."-"."b_in"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftBreakIn"]?>'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"></td>
										<td class='gridDtlVal' align='center'> <input type='text' class='inputs' name=<?=$arr_Day_val."-"."t_out"?> id=<?=$arr_Day_val."-"."t_out"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftTimeOut"]?>' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"></td>
										<td class='gridDtlVal' align='center'>
                                        	<input type="checkbox" class='inputs' name=<?=$arr_Day_val."-"."crossDay"?> id=<?=$arr_Day_val."-"."crossDay"?> value="1" <?php echo ($arr_ShiftCode_Dtl["crossDay"]=="Y"?"checked":""); ?>>
                       					</td>
										<td class='gridDtlVal' align='center'><input type="checkbox" class='inputs' name=<?=$arr_Day_val."-"."restDay"?> id=<?=$arr_Day_val."-"."restDay"?> value="1" <?php echo ($arr_ShiftCode_Dtl["RestDayTag"]=="Y"?"checked":""); ?>></td>
							<?php	
									echo "</tr>";
                                }
							?>
                        </table>
                    </td>
                </tr>
                
                <tr>
                	<td colspan="6"  class='childGridFooter' align="center">
                    	<input type='button' class= 'inputs' name='btnUserDef' value='<?=$btnName?>' onClick="saveShiftDetail();">
                        <input type='button' value='Reset' class='inputs' onClick="reset_page_add();">
                    </td>
                </tr>
            </TABLE>
			
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function saveShiftDetail()
	{
		var shiftInputs = $('frmShiftCode').serialize(true);
		var dayDescr = new Array('','Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		
		//1. Shift Description is required.
		if(shiftInputs["txtShiftDesc"]=="") {
			alert("Shift Description is required.");
			$('txtShiftDesc').focus();
			return false;
		}
			
		
	
		
		for($dayCtr=1; $dayCtr<=7; $dayCtr++)
		{
			
			
			if((shiftInputs[$dayCtr+"-t_in"]!="")||(shiftInputs[$dayCtr+"-l_out"]!="")||(shiftInputs[$dayCtr+"-l_in"]!="")||(shiftInputs[$dayCtr+"-b_out"]!="")||(shiftInputs[$dayCtr+"-b_in"]!="")||(shiftInputs[$dayCtr+"-t_out"]!=""))
			{
				//2. If the user fill up the any field in a certain day, automatic all fields need to be filled up.
				if((shiftInputs[$dayCtr+"-t_in"]=="")||(shiftInputs[$dayCtr+"-l_out"]=="")||(shiftInputs[$dayCtr+"-l_in"]=="")||(shiftInputs[$dayCtr+"-b_out"]=="")||(shiftInputs[$dayCtr+"-b_in"]=="")||(shiftInputs[$dayCtr+"-t_out"]==""))
				{
					alert("Incomplete Shift Time Detail on Day : "+dayDescr[$dayCtr]);
					return false;
				}
				
				//3. Fields only accept military time 01 - 24;
				if((shiftInputs[$dayCtr+"-t_in"]>"24")||(shiftInputs[$dayCtr+"-l_out"]>"24")||(shiftInputs[$dayCtr+"-l_in"]>"24")||(shiftInputs[$dayCtr+"-b_out"]>"24")||(shiftInputs[$dayCtr+"-b_in"]>"24")||(shiftInputs[$dayCtr+"-t_out"]>"24"))
				{
					alert("Incorrect Time Format on Day : "+dayDescr[$dayCtr]+". Shift Code Detail only accepts Military Time.");
					return false;
				}
				
				if((shiftInputs[$dayCtr+"-t_in"]==":")||(shiftInputs[$dayCtr+"-l_out"]==":")||(shiftInputs[$dayCtr+"-l_in"]==":")||(shiftInputs[$dayCtr+"-b_out"]==":")||(shiftInputs[$dayCtr+"-b_in"]==":")||(shiftInputs[$dayCtr+"-t_out"]==":"))
				{
					alert("Incorrect Time Format on Day : "+dayDescr[$dayCtr]);
					return false;
				}
				
				if((shiftInputs[$dayCtr+"-t_in"]!="00:00")||(shiftInputs[$dayCtr+"-l_out"]!="00:00")||(shiftInputs[$dayCtr+"-l_in"]!="00:00")||(shiftInputs[$dayCtr+"-b_out"]!="00:00")||(shiftInputs[$dayCtr+"-b_in"]!="00:00")||(shiftInputs[$dayCtr+"-t_out"]!="00:00"))
				{
					if((shiftInputs[$dayCtr+"-restDay"]=="1")||(shiftInputs[$dayCtr+"-restDay"]=="1")||(shiftInputs[$dayCtr+"-restDay"]=="1")||(shiftInputs[$dayCtr+"-restDay"]=="1")||(shiftInputs[$dayCtr+"-restDay"]=="1")||(shiftInputs[$dayCtr+"-restDay"]=="1"))
					{
						alert("Incorrect Rest Day on Day : "+dayDescr[$dayCtr]);
						return false;
					}
				}
				//4. if Cross Day == Yes, user can encode time which is less than to time in (Sample time in 08:30, lunch out can be 07:30, applicable
			    //   lunch Out, Break Out) else NO OVERLAPPING OF SCHED (PHP CODE)
				
				//5. Lunch Out can be 1 or 2 hours, get sum of lunch out and in, if the sum of 2 is less than / greater than 1 hours, confirm the user.(PHP CODE)
				//6. Summation of the 6 time should be 8 hrs (Regular Day) else if (saturday, Time In and Out ang meron) (PHP CODE)
			}
			
		}
		
		
		params = 'shift_type_maintenance.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmShiftCode').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				RefreshPage();
			}	
		});
		
	}
	
	function conFirmcrossDay($alertMsg,$action)
	{
		var confirmUser = confirm($alertMsg);
		
		if(confirmUser==true)
		{
			params = 'shift_type_maintenance_pop.php?modAction='+$action;
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmShiftCode').serialize(),
				onComplete : function (req){
					eval(req.responseText);
				}	
			});
		}
		else
		{
		}
	}
	
	
	
	function reset_page_add()
	{
		var a = $('frmShiftCode').serialize();
		var c = $('frmShiftCode').serialize(true);
		b = a.split('&');
		
		for(i=4;i<parseInt(b.length)-1;i++){
			d = b[i].split("=");
			document.frmShiftCode[d[0]].value='';
		}
	}
	
</SCRIPT>