<?


session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

include("otApp.obj.php");

$otEditObj = new otAppObj($_GET,$_SESSION);
$sessionVars = $otEditObj->getSeesionVars();
$otEditObj->validateSessions('','MODULES');

$headerTitle = " Overtime Application";
$tableNo = 1;
		
$arr_App = $otEditObj->getTblData("tblTK_OTApp", " and seqNo='".$_GET["inputTypeSeqNo"]."'", "", "sqlAssoc");

/*Action*/
switch($_GET["action"])
{
	case "saveChanges":

		$shiftCodeDtl = $otEditObj->getTblData("tblTk_OTApp", " and empNo='".$arr_App['empNo']."' and otDate='".date("Y-m-d", strtotime($_GET["otDate"]))."' and seqNo != $_GET[inputTypeSeqNo]", "", "sqlAssoc");			
			
		if($shiftCodeDtl["empNo"] != ''){
			echo "'".$shiftCodeDtl["empNo"]."';";
			echo "alert('Duplicate Entry of OT Application.');";
		}else{
			$ret_saveCsSched = $otEditObj->updateOTDtl();
			echo "alert('Succesfully edited OT Application.');";
		}
		exit();
	break;
}

$empInfo = $otEditObj->getUserInfo($_SESSION['company_code'],$arr_App['empNo'],'');
		
$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
$empName = $empInfo["empLastName"].", ".htmlspecialchars(addslashes($empInfo["empFirstName"]))." ".$midName;

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<SCRIPT type="text/javascript" src="../../../includes/calendar.js"></SCRIPT>
        
        
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>
	</HEAD>
	<BODY>
    	<form name="frmPopUp" id="frmPopUp">
        <input type="hidden" name="empbrnCode" id="empbrnCode" value="<?=$empInfo["empBrnCode"]?>" />
        <input type="hidden" name="empPayGrp" id="empPayGrp" value="<?=$empInfo["empPayGrp"]?>" />
        <input type="hidden" name="empPayCat" id="empPayCat" value="<?=$empInfo["empPayCat"]?>" />
		<input type="hidden" name="inputTypeSeqNo" id="inputTypeSeqNo" value="<?=$_GET["inputTypeSeqNo"]?>"/>
    	 
		 <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
         	 <tr style="height:20px;">
                <td align='center' colspan='5' class='prevEmpHeader'>
                    <?="MODIFY".strtoupper($headerTitle)?>
                </td>  
            </tr> 
            
            <tr>
            	<td>
                	<table width="100%" border="0">
                        <tr>
                        	<td>
                            	<table width="806" height="34" border="0">
                                	<tr>
                                    	<td width="93" class="hdrInputsLvl">
                                            Employee  No.
                                        </td>
                                        
                                        <td width="10" class="hdrInputsLvl">
                                            :
                                        </td>
                                        
                                        <td width="158" class="gridDtlVal">
                                            <INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$empInfo["empNo"]?>">
                                        </td>
										
										<td class="hdrInputsLvl" width="102">
                                            Employee Name
                                        </td>
                                        
                                        <td class="hdrInputsLvl" width="10">
                                            :
                                        </td>
                    
                                        <td width="407" colspan="4" class="gridDtlVal">
                                            <INPUT class="inputs" readonly="readonly" type="text" name="txtEmpName" id="txtEmpName" size="40" value="<?=$empName?>">
                                        </td>
                                    </tr>
                                    
                                  
                                </table>
                           
                    </table>
                </td>
            </tr>
        	
            <tr>
            	<td align="center">
                
                    		<table border="1" width="100%" style="border-collapse:collapse;" align="center">
                            	<tr>
                                    <td width="7%" class="gridDtlLbl" align="center">OT DATE</td>
                                    <td width="10%" class="gridDtlLbl" align="center">OT IN</td>
                                    <td width="10%" class="gridDtlLbl" align="center">OT OUT</td>		
                                    <td width="10%" class="gridDtlLbl" align="center">CROSS DATE?</td>
									<td width="20%" class="gridDtlLbl" align="center">PUREPOSE OF OVERTIME</td>
                              
                                   
                                </tr>
                                
                                <tr>
                                    <td  align="center">
                                        <input tabindex="10" class="inputs" type="text" name="otDate" id="otDate" size="10" onFocus="getEmpShift(<?=$empInfo["empNo"]?>);"
                                             value="<?=date("Y-m-d", strtotime($arr_App["otDate"]))?>">
                                        <img src="../../../images/cal_new.png" onClick="displayDatePicker('otDate', this);" style="cursor:pointer;" width="20" height="14">
                                     </td>
                                    
                                 
                                   
                                    <td align="center"><input type="text" class="inputs" name="otIn"  id="otIn" style="width:50%;"onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value="<?=$arr_App["otIn"]?>" /></td>
                                   	<td align="center"><input type="text" class="inputs" name="otOut"  id="otOut" style="width:50%;" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"value="<?=$arr_App["otOut"]?>" /></td>
                                    <td align="center"><input type="checkbox" disabled="disabled" name="crossTag" id="crossTag" <?=($arr_App["crossTag"]=="Y"?"checked":"")?>  /></td>
                                    <td align="center"><span class="gridDtlVal">
                                      <?
										$reasons=$otEditObj->getTblData("tblTK_Reasons "," and stat='A' and ovApp='Y'"," order by reason","sqlArres");
										$arrReasons = $otEditObj->makeArr($reasons,'reason_id','reason','');
										$otEditObj->DropDownMenu($arrReasons,'cmbReasons',$arr_App["otReason"],"class='inputs'");
									?>
											   </span></td>
                                    
                                       </tr>
                            </table>
                  
                    <input type="button" class="inputs" name="btnUpdate" id="btnUpdate" value='UPDATE' onClick="UpdateDetail();">
                </td>
            </tr>
        </table>
        </form>
	</BODY>
</HTML>
<SCRIPT>
	
	function UpdateDetail()
	{
		var appFields = $('frmPopUp').serialize(true);
		
			if((appFields["otIn"]=="")||(appFields["otOut"]==":"))
			{
				alert("Overtime In is required.");
				$('otIn').focus();
				return false;
			}
			
			if((appFields["otIn"]=="")||(appFields["otOut"]==":"))
			{
				alert("Overtime Out is required.");
				$('otOut').focus();
				return false;
			}
			
			if(appFields["cmbReasons"]==0)
			{
				alert("Purpose of Overtime is required.");
				$('cmbReasons').focus();
				return false;
			}
			
			
			
			if(appFields['otOut'] <= appFields['otIn']) {

			var crossTag = appFields['crossTag'];
			
				var blnAnswer = confirm("The OT Out encoded is less than the OT In, is it a cross date?");
					
				if (blnAnswer){
					$('crossTag').checked=true;
					var checked = 'Y'
				}else{
					$('crossTag').checked=false;
					var checked = ''
				}
			}else{
				
				$('crossTag').checked=false;
				var checked = ''
			}
			
			params = 'overtime_popup.php?action=saveChanges&checked='+checked;

		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmPopUp').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			}
		});
		
	}
	
</SCRIPT>