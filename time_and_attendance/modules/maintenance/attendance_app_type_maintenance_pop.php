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

$attendAppType = new maintenanceObj();
$sessionVars = $attendAppType->getSeesionVars();
$attendAppType->validateSessions('','MODULES');


switch($_GET["modAction"])
{
	case "Add":
		$getLastAppCode = $attendAppType->getShiftInfo("tblTK_AppTypes", "", " order by tsAppTypeCd desc");
		$newAttendTypeCode = $getLastAppCode["tsAppTypeCd"] + 1;
		$AttAppCode = ($newAttendTypeCode<10?"0".$newAttendTypeCode:$newAttendTypeCode);
		
		$btnName = "Save";
		$dedTag = $leaveTag = "N";
	break;
	
	case "Edit":
		$arr_AttdnApp_Dtl = $attendAppType->getShiftInfo("tblTK_AppTypes", " and tsAppTypeCd='".$_GET["attdnCode"]."'", " ");
		$AttAppCode= $arr_AttdnApp_Dtl["tsAppTypeCd"];
		$txtAppTypeDesc= $arr_AttdnApp_Dtl["appTypeDesc"];
		$dedTag= $arr_AttdnApp_Dtl["deductionTag"];
		$leaveTag= $arr_AttdnApp_Dtl["leaveTag"];
		$leaveTypeTag = $arr_AttdnApp_Dtl["leaveTypeTag"];
		$cmbAttenAppCodeStat=$arr_AttdnApp_Dtl["appStatus"];
		$readonly = "readonly";
		$btnName = "Update";
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
    	
		<FORM name="frmAttndAppCode" id="frmAttndAppCode" action="<?=$_SERVER['PHP_SELF']?>" method="post">
				
            <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
               
                <tr>
                    <td align='center' colspan='6' class='prevEmpHeader'>
                        Attendance Application Type Details
                    </td>  
                </tr> 
                
                <tr>
                    <td width='40%' class='gridDtlLbl' align='left'>App. Type Code </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal'>
                        <input type='text' class='inputs' name='txtAppTypeCode' id='txtAppTypeCode' style='width:20%;' readonly value=<?=$AttAppCode?> >
                        <input type="hidden" name="action" id="action" value="<?=$btnName?>">
                        
                    </td>
                </tr>
                
                <tr>
                    <td width='40%' class='gridDtlLbl' align='left'>App. Type Desc. </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal' colspan="3">
                        <input type='text' class='inputs' name='txtAppTypeDesc' id='txtAppTypeDesc' style='width:80%;' <?=$readonly?> value='<?=$txtAppTypeDesc?>' maxlength="50">
                    </td>
              		
                </tr>
                
                <tr>
                	<td width='40%' class='gridDtlLbl' align='left'>Deduction Tag </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal'>
                        <input type="radio" name="dedTag" id="dedTag" value="1" <?php echo ($dedTag=="Y"?"checked":""); ?>>Yes
                        <input type="radio" name="dedTag" id="dedTag" value="0" <?php echo ($dedTag=="N"?"checked":""); ?>>No
                        
                    </td>
                </tr>
                
                <tr>
                	<td width='40%' class='gridDtlLbl' align='left'>Leave Tag </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal'>
                        <input type="radio" name="leaveTag" id="leaveTag" value="1" <?php echo ($leaveTag=="Y"?"checked":""); ?>>Yes
                        <input type="radio" name="leaveTag" id="leaveTag" value="0" <?php echo ($leaveTag=="N"?"checked":""); ?>>No
                        
                    </td>
                </tr>
                
                <tr>
                	<td width='40%' class='gridDtlLbl' align='left'>Leave Type Tag </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal'>
                        <input type="radio" name="leaveTypeTag" id="leaveTypeTag" value="1" <?php echo ($leaveTypeTag=="Y"?"checked":""); ?>>Yes
                        <input type="radio" name="leaveTypeTag" id="leaveTypeTag" value="0" <?php echo ($leaveTypeTag=="N"?"checked":""); ?>>No
                        
                    </td>
                </tr>
                
                <tr>
                	 <td width='25%' class='gridDtlLbl' align='left'>Status </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                        <?php 
                            $attendAppType->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbAttenAppCodeStat',$cmbAttenAppCodeStat,'class="inputs" style="width:145px;"');
                        ?>
                    </td>
                </tr>
               
                <tr>
                	<td colspan="6"  class='childGridFooter' align="center">
                    	<input type='button' class= 'inputs' name='btnUserDef' value='<?=$btnName?>' onClick="saveAttendAppDetail();">
                     </td>
                </tr>
            </TABLE>
			
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function saveAttendAppDetail()
	{
		var attAppInputs = $('frmAttndAppCode').serialize(true);
		
		if(attAppInputs["txtAppTypeDesc"]=="")
		{
			alert('Application Type Description is required.');
			$('txtAppTypeDesc').focus();
			return false;
		}
		
		if(attAppInputs["action"]=='Update')
		{
			var confirmUser = confirm("Are you sure you want to Update the selected Attendance Application Code?");
			if(confirmUser==true)
			{
				params = 'attendance_app_type_maintenance.php';
				new Ajax.Request(params,{
					method : 'get',
					parameters : $('frmAttndAppCode').serialize(),
					onComplete : function (req){
						eval(req.responseText);
					}	
				});
			}
			else
			{
				return false;
			}
		}
		else
		{
			params = 'attendance_app_type_maintenance.php';
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmAttndAppCode').serialize(),
				onComplete : function (req){
					eval(req.responseText);
				}	
			});
		}
		
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
	
	
	
	
</SCRIPT>