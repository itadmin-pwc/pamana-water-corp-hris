<?
/*
	Date Created	:	07272010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$empShiftMaint = new maintenanceObj();
$sessionVars = $empShiftMaint->getSeesionVars();
$empShiftMaint->validateSessions('','MODULES');

$arr_Day = array(1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday');

$arr_empInfo = $empShiftMaint->getUserInfo($_SESSION["company_code"],$_GET["empNo"],'');


switch($_GET["action"])
{
	case "dispShiftDtl":
		//echo "$('testfield').value=1';";
		for($dayI=1; $dayI<=7; $dayI++)
		{
			$arr_ShiftCode_Dtl = $empShiftMaint->getShiftInfo("tblTK_ShiftDtl", " and shftCode='".$_GET['shiftcode']."' and dayCode='".$dayI."'", " ");
            echo "document.frmEmpShift.".$arr_Day[$dayI]."_t_in.value='{$arr_ShiftCode_Dtl['shftTimeIn']}';";
			echo "document.frmEmpShift.".$arr_Day[$dayI]."_l_out.value='{$arr_ShiftCode_Dtl['shftLunchOut']}';";
			echo "document.frmEmpShift.".$arr_Day[$dayI]."_l_in.value='{$arr_ShiftCode_Dtl['shftLunchIn']}';";
			echo "document.frmEmpShift.".$arr_Day[$dayI]."_b_out.value='{$arr_ShiftCode_Dtl['shftBreakOut']}';";
			echo "document.frmEmpShift.".$arr_Day[$dayI]."_b_in.value='{$arr_ShiftCode_Dtl['shftBreakIn']}';";
			echo "document.frmEmpShift.".$arr_Day[$dayI]."_t_out.value='{$arr_ShiftCode_Dtl['shftTimeOut']}';";
			
		}
		//
			exit();    		
		//echo "document.frmEmpShift.testfield.value = 1";
		
		
		
	break;
	
	case "Add":
		
		$btnName = "Save";
		$arr_empBioInfo = $empShiftMaint->getShiftInfo("tblBioEmp", " and empNo='".($_GET["empNo"]!=""?$_GET["empNo"]:$_GET["txtEmpNo"])."'", " ");
		$empBioNum = $arr_empBioInfo["bioNumber"];
		
		$arr_chkRankLevelTExempt = $empShiftMaint->getShiftInfo("tblTK_RankLevelTimeExempt", "  and exemptRankCd='".$arr_empInfo["empRank"]."' and exemptLevelCd='".$arr_empInfo["empLevel"]."'", " ");
		
		//$chkAbsentExemptTag = $arr_chkRankLevelTExempt["absentExempt"];
		$chkOtExemptTag = $arr_chkRankLevelTExempt["otExempt"];
		$chkFlexiExemptTag = $arr_chkRankLevelTExempt["utHrsExempt"];
		$chkWrkHrsExemptTag = $arr_chkRankLevelTExempt["trdHrsExempt"];
		$gp = 15;
		//$chkLunchExemptTag = $arr_chkRankLevelTExempt["lunchHrsExempt"];
		
		//echo $arr_chkRankLevelTExempt["utHrsExempt"]."GEN";
	break;
	
	case "Edit":
		$btnName = "Update";
		
		$arr_EmpShiftDtl = $empShiftMaint->getShiftInfo('tblTK_EmpShift', " and empNo='".$_GET["empNo"]."'", $orderBy);
		$empBioNum = $arr_EmpShiftDtl["bioNo"];
		$cmbShitCodeStat = $arr_EmpShiftDtl["status"];
		$shiftcode = $arr_EmpShiftDtl["shiftCode"];
		//$chkAbsentExemptTag = $arr_EmpShiftDtl["absentExempt"];
		$chkOtExemptTag = $arr_EmpShiftDtl["otExempt"];
		$chkFlexiExemptTag = $arr_EmpShiftDtl["utHrsExempt"];
		$chkWrkHrsExemptTag = $arr_EmpShiftDtl["trdHrsExempt"];
		$gp = $arr_EmpShiftDtl["gracePeriod"];
		//$chkLunchExemptTag = $arr_EmpShiftDtl["lunchHrsExempt"];
		
	break;
	
	case "Save":
	
		//Check if tne Emp. exists in tblTk_EmpShift
		$chkDuplicate = $empShiftMaint->getShiftInfo("tblTK_EmpShift", " and empNo='".$_GET["txtEmpNo"]."'", " ");
		
		if($chkDuplicate["shiftCode"]!="")
		{
			echo "alert('Record already Exists.');";
		}
		else
		{
			if($empShiftMaint->maint_EmpShift("Add",$_GET))
				echo "alert('Employee Shift Successfully Added.');";
			else
				echo "alert('Error in Adding Employee Shift.');";
		}
		exit();
	break;
	
	case "Update":
		if($empShiftMaint->maint_EmpShift("Update",$_GET))
			echo "alert('Employee Shift Successfully Updated.');";
		else
			echo "alert('Error in Updating Employee Shift.');";
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
    	
		<FORM name="frmEmpShift" id="frmEmpShift" action="<?=$_SERVER['PHP_SELF']?>" method="post">
		<input type="hidden" name="action" id="action" value="<?=$btnName?>">	
        
            <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
               
                <tr>
                    <td align='center' colspan='6' class='prevEmpHeader'>
                        Employee Shift Details
                    </td>  
                </tr> 
                
                 <tr>
                    <td width='15%' class='gridDtlLbl' align='left'>Employee Name </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                        <input type='text' class='inputs' name='txtEmpName' id='txtEmpName' style='width:100%;' readonly value='<?=$arr_empInfo["empLastName"].", ".$arr_empInfo["empFirstName"]." ".$arr_empInfo["empMidName"][0]."."?>' >
                    </td>
                    
                    <td width='15%' class='gridDtlLbl' align='left'>Employee No. </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                       <input type='text' class='inputs' name='txtEmpNo' id='txtEmpNo' style='width:50%;' readonly value='<?=$_GET["empNo"]?>' >
                    </td>
                </tr>
                
                <tr>
                    <td width='15%' class='gridDtlLbl' align='left'>Bio - Number </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                        <input type='text' class='inputs' name='txtEmpBio' id='txtEmpBio' style='width:60%;' value='<?=$empBioNum?>' readonly onKeyDown="javascript:return dFilter (event.keyCode, this, '####');" >
                    </td>
                    
                    <td width='25%' class='gridDtlLbl' align='left'>Branch </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                    <?
                    $empBrn=$empShiftMaint->getShortBranchName($_SESSION["company_code"],$arr_empInfo['empBrnCode']);
					echo $empBrn;
					?>
                    </td>
               </tr>
               
               <tr>
                    <td width='15%' class='gridDtlLbl' align='left'>Shift  </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                    	
                        
							<? 	
								if($arr_empInfo['empBrnCode']!=="0001"){
									$shiftHeader = 	$empShiftMaint->getListShift();
								}
								else{
									$shiftHeader = $empShiftMaint->getListShiftBranch();	
								}			
                                $arrShifts = $empShiftMaint->makeArr($shiftHeader,'shiftCode','shiftDesc','');
                                $empShiftMaint->DropDownMenu($arrShifts,'shiftcode',$shiftcode,"onChange=\"getShiftCodeDetail();\"");
                            ?>
                       
                    </td>
                    <td width='25%' class='gridDtlLbl' align='left'>CWW Employee </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
					<?
						if($btnName=="Save"){
							if($arr_empInfo['empBrnCode']!=="0001"){
									$cwwtag = "Y";
									$chkStyle = "";
							}
							else{
								$cwwtag = "";
								$chkStyle = 'disabled="disabled"';
							}
						}
						else{
							if($arr_empInfo['empBrnCode']!=="0001"){
								$cwwCheck = $empShiftMaint->getShiftInfo('tblTK_EmpShift', " and empNo='".$arr_empInfo["empNo"]."'", '');
								if($cwwCheck['CWWTag']=="Y"){					
									$cwwtag = $cwwCheck['CWWTag'];
									$chkStyle = "";
								}
								else{
									$cwwtag = "";	
									$chkStyle = "";	
								}
							}
							else{
								$cwwtag = "";	
								$chkStyle = 'disabled="disabled"';
							}	
						}
                    ?>
                    <td width='25%' class='gridDtlVal'><input type="checkbox" name="chkCWWTag" value="1" <? echo($cwwtag=="Y"?"checked":"");?> <? echo $chkStyle;?>/></td>
               </tr>
               
                <tr>
                	<td colspan="6" align="left">
                    	<table border="1" width="100%" style="border-collapse:collapse;">
                        	<tr align="center" class="gridDtlLbl">
                            	<td width="10%" colspan="2"></td>
                                <td width="10%" colspan="2">LUNCH</td>
                                <td width="10%" colspan="2">BREAK</td>
                                <td width="10%" ></td>
                            </tr>
                            
                            <div id="shiftCodeDtl">
                        	<tr align="center" class="gridDtlLbl">
                            	<td width="10%">Day</td>
                                <td width="10%">Time - In</td>
                                <td width="10%">OUT</td>
                                <td width="10%">IN</td>
                            	<td width="10%">OUT</td>
                                <td width="10%">IN</td>
                                <td width="10%">Time - Out</td>
                                
                            </tr>
                            
                                <?php
									
                                    foreach($arr_Day as $arr_Day_val=>$dayDesc)
                                    {
                                        echo "<tr>";
                                        $arr_ShiftCode_Dtl = $empShiftMaint->getShiftInfo("tblTK_ShiftDtl", " and shftCode='".$shiftcode."' and dayCode='".$arr_Day_val."'", " ");
                                			
								?>		
                                
                                            <td class='gridDtlVal' id=<?= $arr_Day_val?><font class='gridDtlLblTxt'><?=$dayDesc?></font></td>
                                            <td class='gridDtlVal' align='center'> <input type='text' class='inputs' name='<?=$dayDesc."_"."t_in"?>' id='<?=$dayDesc."_"."t_in"?>' style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftTimeIn"]?>'  readonly ></td>
                                            <td class='gridDtlVal' align='center'> <input type='text' class='inputs' name='<?=$dayDesc."_"."l_out"?>' id=<?=$dayDesc."_"."l_out"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftLunchOut"]?>'  readonly></td>
                                            <td class='gridDtlVal' align='center'> <input type='text' class='inputs' name='<?=$dayDesc."_"."l_in"?>' id=<?=$dayDesc."_"."l_in"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftLunchIn"]?>'  readonly></td>
                                            <td class='gridDtlVal' align='center'> <input type='text' class='inputs' name='<?=$dayDesc."_"."b_out"?>' id=<?=$dayDesc."_"."b_out"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftBreakOut"]?>'  readonly></td>
                                            
                                            <td class='gridDtlVal' align='center'> <input type='text' class='inputs' name='<?=$dayDesc."_"."b_in"?>' id=<?=$dayDesc."_"."b_in"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftBreakIn"]?>'  readonly></td>
                                            <td class='gridDtlVal' align='center'> <input type='text' class='inputs' name='<?=$dayDesc."_"."t_out"?>' id=<?=$dayDesc."_"."t_out"?> style='width:50%;' value='<?=$arr_ShiftCode_Dtl ["shftTimeOut"]?>' readonly></td>
                                    		
                                <?php	
                                        echo "</tr>";
                                    }
                                ?>
                             </div>
                        </table>
                        <br>
                    </td>
                </tr>
                
                <tr>
                	<td colspan="6" >
                    	<table border="1" width="100%" style="border-collapse:collapse;">
                        	<tr class='gridDtlVal'>
                            	<td><input type="checkbox" name="chkWrkHrsExemptTag" onClick="return readOnlyCheckBox();"  value="1" <?php echo ($chkWrkHrsExemptTag=='Y'?"checked":"");?>></td>
                                <td><font class='gridDtlLblTxt'><b>Tardy Hrs. Exempt</font></td>
                                <td><input type="checkbox" name="chkFlexiExemptTag" onClick="return readOnlyCheckBox();"   value="1" <?php echo ($chkFlexiExemptTag=='Y'?"checked":"");?>></td>
                                <td><font class='gridDtlLblTxt'><b>UT Time  Exempt</font></td>
                                 <td><input type="checkbox" name="chkOtExemptTag" onClick="return readOnlyCheckBox();"   value="1" <?php echo ($chkOtExemptTag=='Y'?"checked":"");?>></td>
                                <td><font class='gridDtlLblTxt'><b>Fixed Schedule</font></td>
                                <td><font class='gridDtlLblTxt' style="margin-right: 10px;"><b>Grace Period (No. in Min)</font>
								<input type="text" name="gracePeriod" style="width: 30px;" value="<?=$gp?>"></td>
                            </tr>
                        </table>
                         <br>
                    </td>
                </tr>
                
                <tr>
                	<td colspan="6"  class='childGridFooter' align="center">
                    	<input type='button' class= 'inputs' name='btnUserDef' value='<?=$btnName?>' onClick="validation();">
                        
                    </td>
                </tr>
            </TABLE>
			
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function readOnlyCheckBox() {
	   return true;
	}

	function getShiftCodeDetail()
	{
		
		//document.frmEmpShift.submit();
		var shiftCode = document.frmEmpShift.shiftcode.value;
		new Ajax.Request(
		  'employee_shift_maintenance_pop.php?action=dispShiftDtl&shiftcode='+shiftCode,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
					eval(req.responseText);
			 }
		  }
		);
		
		
	}
	
	function validation()
	{
		var empShiftInputs = $('frmEmpShift').serialize(true);
		
		if(empShiftInputs["txtEmpBio"]=="")
		{
			alert('Bio - Number is required.');
			$('txtEmpBio').focus();
			return false;
		}
		
		if(empShiftInputs["shiftcode"]=="0")
		{
			alert('Shift Code is required.');
			$('shiftcode').focus();
			return false;
		}
		
		params = 'employee_shift_maintenance_pop.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmEmpShift').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}
	
	
	
</SCRIPT>