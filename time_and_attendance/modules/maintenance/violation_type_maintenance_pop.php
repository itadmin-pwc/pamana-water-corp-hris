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

$vioType = new maintenanceObj();
$sessionVars = $vioType->getSeesionVars();
$vioType->validateSessions('','MODULES');


switch($_GET["modAction"])
{
	case "Add":
		$getLastVioCode = $vioType->getShiftInfo("tblTK_ViolationType", "", " order by violationCd desc");
		$newVioCode = $getLastVioCode["violationCd"] + 1;
		$vioCode = ($newVioCode<10?"0".$newVioCode:$newVioCode);
		
		$btnName = "Save";
	break;
	
	case "Edit":
		$arr_VioCode_Dtl = $vioType->getShiftInfo("tblTK_ViolationType", " and violationCd='".$_GET["vioCode"]."'", " ");
		$vioCode= $arr_VioCode_Dtl["violationCd"];
		$txtVioTypeShrtDesc = $arr_VioCode_Dtl["violationShortDesc"];
		$txtVioTypeDesc= $arr_VioCode_Dtl["violationDesc"];
		$cmbVioCodeStat = $arr_VioCode_Dtl["violationStat"];
		
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
    	
		<FORM name="frmVioAppCode" id="frmVioAppCode" action="<?=$_SERVER['PHP_SELF']?>" method="post">
				
            <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
               
                <tr>
                    <td align='center' colspan='6' class='prevEmpHeader'>
                       Violation Code Details
                    </td>  
                </tr> 
                
                <tr>
                    <td width='40%' class='gridDtlLbl' align='left'>Violation Code </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal'>
                        <input type='text' class='inputs' name='txtVioCode' id='txtVioCode' style='width:20%;' readonly value=<?=$vioCode?> >
                        <input type="hidden" name="action" id="action" value="<?=$btnName?>">
                        
                    </td>
                </tr>
                
                <!--<tr>
                    <td width='40%' class='gridDtlLbl' align='left'>Violation Short Desc. </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal' colspan="3">
                        <input type='text' class='inputs' name='txtVioTypeShrtDesc' id='txtVioTypeShrtDesc' style='width:80%;' <?=$readonly?> value='<?=$txtVioTypeShrtDesc?>' maxlength="50">
                    </td>
              		
                </tr>-->
                
                <tr>
                    <td width='40%' class='gridDtlLbl' align='left'>Violation Type Desc. </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='60%' class='gridDtlVal' colspan="3">
                        <input type='text' class='inputs' name='txtVioTypeDesc' id='txtVioTypeDesc' style='width:80%;' <?=$readonly?> value='<?=$txtVioTypeDesc?>' maxlength="50">
                    </td>
              		
                </tr>
                
              
                <tr>
                	 <td width='25%' class='gridDtlLbl' align='left'>Status </td>
                    <td width='1%' class='gridDtlLbl' align='center'>:</td>
                        
                    <td  width='25%' class='gridDtlVal'>
                        <?php 
                            $vioType->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbVioCodeStat',$cmbVioCodeStat,'class="inputs" style="width:145px;"');
                        ?>
                    </td>
                </tr>
               
                <tr>
                	<td colspan="6"  class='childGridFooter' align="center">
                    	<input type='button' class= 'inputs' name='btnUserDef' value='<?=$btnName?>' onClick="saveVioCodeDetail();">
                     </td>
                </tr>
            </TABLE>
			
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function saveVioCodeDetail()
	{
		var attAppInputs = $('frmVioAppCode').serialize(true);
		
		if(attAppInputs["txtVioTypeShrtDesc"]=="")
		{
			alert('Violation Type Short Description is required.');
			$('txtVioTypeShrtDesc').focus();
			return false;
		}
		
		if(attAppInputs["txtVioTypeDesc"]=="")
		{
			alert('Violation Type Description is required.');
			$('txtVioTypeDesc').focus();
			return false;
		}
		
		if(attAppInputs["action"]=='Update')
		{
			var confirmUser = confirm("Are you sure you want to Update the selected Violation Code?");
			if(confirmUser==true)
			{
				params = 'violation_type_maintenance.php';
				new Ajax.Request(params,{
					method : 'get',
					parameters : $('frmVioAppCode').serialize(),
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
			params = 'violation_type_maintenance.php';
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmVioAppCode').serialize(),
				onComplete : function (req){
					eval(req.responseText);
				}	
			});
		}
		
	}
	
	
	
	
	
	
</SCRIPT>