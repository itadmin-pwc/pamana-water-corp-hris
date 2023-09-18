<?


session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

include("leaveApp.obj.php");

$lvEditObj = new leaveAppObj($_GET,$_SESSION);
$sessionVars = $lvEditObj->getSeesionVars();
$lvEditObj->validateSessions('','MODULES');

$headerTitle = " Leave Application";
$tableNo = 1;
		
$arr_App = $lvEditObj->getTblData("tblTK_LeaveApp", " and seqNo='".$_GET["inputTypeSeqNo"]."'", "", "sqlAssoc");

$qryGetType = "SELECT * FROM tblTK_LeaveApp INNER JOIN
                    tblTK_AppTypes WHERE LeaveTypetag='Y' Order by Apptypeshortdesc
					";
		
	$resType = $lvEditObj->execQry($qryGetType);
	$arrType = $lvEditObj->getArrRes($resType);
	$arrAppType = $lvEditObj->makeArr($arrType,'tsAppTypeCd','appTypeDesc','');


/*Action*/
switch($_GET["action"])
{
	case "saveChanges":

		$shiftCodeDtl = $lvEditObj->getTblData("tblTk_LeaveApp", " and empNo='".$arr_App['empNo']."' and lvDateFrom='".date("Y-m-d", strtotime($_GET["lvDateFrom"]))."' and lvDateTo ='".date("Y-m-d", strtotime($_GET["lvDateTo"]))."' and seqNo != $_GET[inputTypeSeqNo]", "", "sqlAssoc");			
	
		if($shiftCodeDtl["empNo"] != ''){
			echo "'".$shiftCodeDtl["empNo"]."';";
			echo "alert('Duplicate Entry of Leave Application.');";
		}else{
			$ret_saveCsSched = $lvEditObj->updateLeaveDtl();
			echo "alert('Succesfully edited Leave Application.');";
		}
		exit();
	break;
}

$empInfo = $lvEditObj->getUserInfo($_SESSION['company_code'],$arr_App['empNo'],'');
		
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
    	<form name="frmPopUp_Leave" id="frmPopUp_Leave">
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
            	<td><table width="100%" height="34" border="0">
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
                </td>
            </tr>
        	
            <tr>
            	<td align="center">
                
                    		<table border="1" width="100%" style="border-collapse:collapse;" align="center">
                            	<tr>
                                    
           							<td width="22%" class="gridDtlLbl" align="center">FROM</td>
            						<td width="22%" class="gridDtlLbl" align="center">TO</td>
            						<td width="10%" class="gridDtlLbl" align="center">DEDUCT TAG</td>
            						<td width="24%" class="gridDtlLbl" align="center">LEAVE TYPE</td>
									<td width="22%" class="gridDtlLbl" align="center">REASON FOR FILING</td>
                                </tr>
                                
                                <tr>
                                    <td  align="center">
                                        <input tabindex="10" class="inputs" type="text" name="lvDateFrom" id="lvDateFrom" size="10" onFocus="getEmpShift(<?=$empInfo["empNo"]?>);"
                                             value="<?=date("Y-m-d", strtotime($arr_App["lvDateFrom"]))?>">
           						 			<img src="../../../images/cal_new.png" onClick="displayDatePicker('lvDateFrom', this);" style="cursor:pointer;" width="20" height="14">		
										
										<?
											$lvEditObj->DropDownMenu(array('WD'=>'WD','AM' => 'HF AM','PM' => 'HF PM'),'cmbFromAMPM',$lvAMPMFrom,'class="inputs" tabindex="12"');
										?>	
									</td>
   
                                    <td align="center"><input type="text" class="inputs" name="lvDateTo"  id="lvDateTo" size="10" value="<?=date("Y-m-d", strtotime($arr_App["lvDateTo"]))?>" /> 
											<img src="../../../images/cal_new.png" onClick="displayDatePicker('lvDateTo', this);" style="cursor:pointer;" width="20" height="14">
										<?
											$lvEditObj->DropDownMenu(array('WD'=>'WD','AM' => 'HF AM','PM' => 'HF PM'),'cmbToAMPM',$lvAMPMTo,'class="inputs" tabindex="16"');
										?>
									</td>
                                    <td align="center"><span class="gridDtlVal">
                       
                                    <?
							$lvEditObj->DropDownMenu(array(''=>'','Y' => 'Yes'),'chkDeduct',$arr_App["deductTag"],'class="inputs" tabindex="16"');
						?>
                                  </span></td>
                                   	<td align="center">
										<?=$lvEditObj->DropDownMenu($arrAppType,'tsAppTypeCd',$arr_App["tsAppTypeCd"],'class="inputs" style="width:90%;"');?>

									</td>
                                    <td align="center"><span class="gridDtlVal">
                                    <?
						$reasons=$lvEditObj->getTblData("tblTK_Reasons "," and stat='A' and leaveApp='Y'"," order by reason","sqlArres");
						$arrReasons = $lvEditObj->makeArr($reasons,'reason_id','reason','');
						$lvEditObj->DropDownMenu($arrReasons,'cmbReasons',$arr_App["lvReason"],"class='inputs'");
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
	
		var appFields = $('frmPopUp_Leave').serialize(true);
		var lvFrom = appFields['cmbFromAMPM'];
		var lvTo = appFields['cmbToAMPM'];
		var lvReason = appFields['cmbReasons'];
		//var lvReturn = appFields['cmbReturnAMPM'];
	
			
			
			if(appFields["tsAppTypeCd"]==0)
			{
				alert("Application type is required.");
				$('tsAppTypeCd').focus();
				return false;
			}
			
			if(appFields["cmbReasons"]==0)
			{
				alert("Reason for Filing is required.");
				$('cmbReasons').focus();
				return false;
			}

			params = 'leave_popup.php?action=saveChanges&lvFrom='+lvFrom+'&lvTo='+lvTo+'&lvReason='+lvReason;

		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmPopUp_Leave').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			}
		});
		
	}
	
</SCRIPT>