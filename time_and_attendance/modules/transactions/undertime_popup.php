<?


session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

include("utApp.obj.php");

$utEditObj = new utAppObj($_GET,$_SESSION);
$sessionVars = $utEditObj->getSeesionVars();
$utEditObj->validateSessions('','MODULES');

$headerTitle = " Undertime Application";
$tableNo = 1;
		
$arr_App = $utEditObj->getTblData("tblTK_UTApp", " and seqNo='".$_GET["inputTypeSeqNo"]."'", "", "sqlAssoc");

/*Action*/
switch($_GET["action"])
{
	case "saveChanges":

		$shiftCodeDtl = $utEditObj->getTblData("tblTk_UTApp", " and empNo='".$arr_App['empNo']."' and utDate='".date("m/d/Y", strtotime($_GET["utDate"]))."' and seqNo != $_GET[inputTypeSeqNo]", "", "sqlAssoc");			
			
		if($shiftCodeDtl["empNo"] != ''){
			echo "'".$shiftCodeDtl["empNo"]."';";
			echo "alert('Duplicate Entry of UT Application.');";
		}else{
			$ret_saveCsSched = $utEditObj->updateUTDtl();
			echo "alert('Succesfully edited UT Application.');";
		}
		exit();
	break;
	
	case 'checkShift':
		$shiftCodeDtl = $utEditObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("m/d/Y", strtotime($_GET["dateUt"]))."'", "", "sqlAssoc");
		if($shiftCodeDtl["timeOut"] != ''){
			echo "$('txtSched').value='".$shiftCodeDtl["shftTimeOut"]."';";
		}else{
			echo "alert('Employee has no Shift Time Out on this date.');";
		}
		exit();
	break;
	
}

$empInfo = $utEditObj->getUserInfo($_SESSION['company_code'],$arr_App['empNo'],'');
		
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
                                    <td width="18%" class="gridDtlLbl" align="center">UNDERTIME DATE</td>
                                    <td width="21%" class="gridDtlLbl" align="center">OFFICIAL SCHEDULE</td>
                                    <td width="21%" class="gridDtlLbl" align="center">TIME OF DEPARTURE</td>		
                                    <td width="40%" class="gridDtlLbl" align="center">REASON OF UNDERTIME</td>
                              
                                   
                                </tr>
                                
                                <tr>
                                    <td  align="center">
                                        <input tabindex="10" class="inputs" type="text" name="dateUt" id="dateUt" size="10" onKeyDown="checkShift(event,this.event)", readonly value="<?=date("m/d/Y", strtotime($arr_App["utDate"]))?>">
                                        <img src="../../../images/cal_new.png" onClick="displayDatePicker('dateUt', this);" style="cursor:pointer;" width="20" height="14">
                                     </td>
                                    
                                 
                                   
                                    <td align="center"><input type="text" class="inputs" name="txtSched"  id="txtSched" style="width:50%;"onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value="<?=$arr_App["offTimeOut"]?>" /></td>
                                   	<td align="center"><input type="text" class="inputs" name="txtUtOut"  id="txtUtOut" style="width:50%;" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"value="<?=$arr_App["utTimeOut"]?>" /></td>
                                    <td align="center"><span class="gridDtlVal">
                                      <?
										$reasons=$utEditObj->getTblData("tblTK_Reasons "," and stat='A' and underTime='Y'"," order by reason","sqlArres");
										$arrReasons = $utEditObj->makeArr($reasons,'reason_id','reason','');
										$utEditObj->DropDownMenu($arrReasons,'cmbReasons',$arr_App["utReason"],"class='inputs'");
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
		
			if(appFields["dateUt"]=="")
			{
				alert("Undertime date is required.");
				$('dateUt').focus();
				return false;
			}
			
			if(appFields["txtSched"]=="")
			{
				alert("Official schedule is required.");
				$('txtSched').focus();
				return false;
			}

			if(appFields["txtUtOut"]=="")
			{
				alert("Time of departure is required.");
				$('txtUtOut').focus();
				return false;
			}			
			
			if (appFields['txtUtOut'].substr(0,2) > '24') {
				alert('Departure Time is invalid');
				return false;
			}
			
			if(appFields['txtUtOut'].substr(3,2) > '59') {
				alert('Departure Time is invalid');
				return false;
			}			
			
			if(appFields['txtUtOut'].substr(0,2) > appFields['txtSched'].substr(0,2)) {
				alert('Time of Departure is greater than the Scheduled Time Out');
				
				return false;
			}	
			
			if(appFields["cmbReasons"]==0)
			{
				alert("Purpose of Undertime is required.");
				$('cmbReasons').focus();
				return false;
			}
			
			
			params = 'undertime_popup.php?action=saveChanges';

		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmPopUp').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			}
		});
		
	}
	
	function checkShift(){
	var arrEle = $('frmPopUp').serialize(true);
	var dateOt =(arrEle['dateUt']);
	var empNo = arrEle['txtAddEmpNo'];

	var param = '&empNo='+empNo+'&dateUt='+dateOt;
	
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=checkShift'+param,{
				method : 'get',
				parameters : $('frmPopUp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					//pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('btnUpdate').value='Processing....';
				},
				onSuccess : function (){
					$('btnUpdate').value='UPDATE';
				}
			});	
	
	}
	
</SCRIPT>