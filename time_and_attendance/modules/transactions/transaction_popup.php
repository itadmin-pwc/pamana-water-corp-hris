<?
/*
	Date Created	:	10062010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

include("transaction_obj.php");

$tPopUpObj = new transactionObj();
$sessionVars = $tPopUpObj->getSeesionVars();
$tPopUpObj->validateSessions('','MODULES');


/*Module User Interface*/
switch($_GET["moduleName"])
{
	case "ChangeShift":
		$headerTitle = " Change Shift Application";
		$tableNo = 1;
		
		$arr_App = $tPopUpObj->getTblData("tblTK_CSApp", " and seqNo='".$_GET["inputTypeSeqNo"]."'", "", "sqlAssoc");
	break;
	
	case "ChangeRestDay":
		$headerTitle = " Change Rest Day Application";
		$tableNo = 2;
		
		$arr_App = $tPopUpObj->getTblData("tblTK_ChangeRDApp", " and seqNo='".$_GET["inputTypeSeqNo"]."'", "", "sqlAssoc");
	break;
	
	case "ChangeOB":
		$headerTitle = " Change OB Application";
		$tableNo = 3;
		
		$arr_App = $tPopUpObj->getTblData("tblTK_OBApp", " and seqNo='".$_GET["inputTypeSeqNo"]."'", "", "sqlAssoc");
		
		$queryBrnches = "Select * from tblBranch as tblbrn where compCode='".$_SESSION["company_code"]."' and brnStat='A'
					order by brnDesc";
		
		$resBrnches = $tPopUpObj->execQry($queryBrnches);
		$arrBrnches = $tPopUpObj->getArrRes($resBrnches);
		$arrBrnch = $tPopUpObj->makeArr($arrBrnches,'brnCode','brnDesc','Others');
	break;
	
}

/*Action*/
switch($_GET["action"])
{
	case "saveChanges1":
		$ret_saveCsSched = $tPopUpObj->validateTran_Cd($_GET, "Update");
		//echo "alert('$ret_saveCsSched');";
		if($ret_saveCsSched){
			echo "alert('CS Application has been updated!');";	
		}
		else{
			echo "alert('Failed to update CS Application!');";		
		}
		exit();
	break;
}

$empInfo = $tPopUpObj->getUserInfo($_SESSION['company_code'],$arr_App['empNo'],'');
		
		
$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
$empName = $empInfo["empLastName"].", ".htmlspecialchars(addslashes($empInfo["empFirstName"]))." ".$midName;

$deptName = $tPopUpObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
$posName = $tPopUpObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');

$deptPos = htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"];
			

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
                <td align='center' colspan='6' class='prevEmpHeader'>
                    <?="MODIFY".strtoupper($headerTitle)?>
                </td>  
            </tr> 
            
            <tr>
            	<td>
                	<table border="0" width="100%">
                    	<tr>
                        	<td></td>
                        </tr>
                        
                        <tr>
                        	<td>
                            	<table border="0" width="100%">
                                	<tr>
                                    	<td class="hdrInputsLvl">
                                            Employee  No.
                                        </td>
                                        
                                        <td class="hdrInputsLvl">
                                            :
                                        </td>
                                        
                                        <td width="604" class="gridDtlVal">
                                            <INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$empInfo["empNo"]?>">
                                        <input tabindex="10" class="inputs" type="hidden" name="dateFiled" readonly="readonly" id="dateFiled" size="10"
                                             value="<?=date("Y-m-d", strtotime($arr_App["dateFiled"]))?>"
                                                        ></td>
                                    </tr>
                                    
                                    <tr>
                                    	<td class="hdrInputsLvl" width="155">
                                            Employee Name
                                        </td>
                                        
                                        <td class="hdrInputsLvl" width="10">
                                            :
                                        </td>
                    
                                        <td class="gridDtlVal" colspan="4">
                                            <INPUT class="inputs" readonly="readonly" type="text" name="txtEmpName" id="txtEmpName" size="40" value="<?=$empName?>">
                                        </td>
                                        
                                        <td class="hdrInputsLvl" width="103">&nbsp;</td>
                                        
                                        <td class="hdrInputsLvl" width="16">&nbsp;</td>
                    
                                        <td width="28" colspan="4" class="gridDtlVal">&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td class="hdrInputsLvl">Dept. / Position </td>
                                      <td class="hdrInputsLvl">: </td>
                                      <td class="gridDtlVal" colspan="4"><input class="inputs" readonly="readonly" type="text" name="txtDeptPost" id="txtDeptPost" size="60" value="<?=$deptPos?>"></td>
                                      <td class="hdrInputsLvl">&nbsp;</td>
                                      <td class="hdrInputsLvl">&nbsp;</td>
                                      <td class="gridDtlVal" colspan="4">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        	
            <tr>
            	<td align="center">
                	<?
						if($tableNo==1)
						{
					?>
                    		<table border="1" width="100%" style="border-collapse:collapse;">
                            	<tr style="height:25px;">
                                    <td class="gridDtlLbl" align="center" colspan="3">FROM</td>
                                    <td class="gridDtlLbl" align="center" colspan="3">TO</td>
                                    <td class="gridDtlLbl" align="center" >CROSS DATE</td>
                                    <td class="gridDtlLbl" align="center" >REMARKS / REASON(S)</td>		
                                   
                                </tr>
                                
                                <tr style="height:20px;">
                                    <td width="10%" class="gridDtlLbl" align="center">DATE</td>
                                    <td width="6%" class="gridDtlLbl" align="center">IN</td>		
                                    <td width="6%" class="gridDtlLbl" align="center">OUT</td>
                                    <td width="10%" class="gridDtlLbl" align="center">DATE</td>
                                    <td width="6%" class="gridDtlLbl" align="center">IN</td>		
                                    <td width="6%" class="gridDtlLbl" align="center">OUT</td>
                                   <!-- <td width="8%" class="gridDtlLbl" align="center"></td>-->
                                   <td width="5%" class="gridDtlLbl" align="center"></td>
                                    <td width="15%" class="gridDtlLbl" align="center"></td>
                                  
                                </tr>
                                
                                <tr>
                                    <td  align="center">
                                        <input tabindex="10" class="inputs" type="text" name="csDateFrom" readonly="readonly" id="csDateFrom" size="10" 
                                             value="<?=date("Y-m-d", strtotime($arr_App["csDateFrom"]))?>" >
                                                    
                                                        
                                    
                                    </td>
                                    <td><input type="text" readonly="readonly" class="inputs" name="schedTimeIn"  id="schedTimeIn" style="width:100%;" value="<?=$arr_App["csShiftFromIn"]?>" /></td>
                                    <td><input type="text" readonly="readonly" class="inputs" name="schedTimeOut"  id="schedTimeOut" style="width:100%;" value="<?=$arr_App["csShiftFromOut"]?>" /></td>
                                  
                                    <td  align="center">
                                        <input tabindex="10" class="inputs" type="text" name="csDateTo" readonly="readonly" id="csDateTo" size="10"
                                             value="<?=date("Y-m-d", strtotime($arr_App["csDateTo"]))?>" >
                                                    
                                                        <img src="../../../images/cal_new.png" onClick="displayDatePicker('csDateTo', this);" style="cursor:pointer;" width="20" height="14">
                                    
                                    </td>
                                    
                                     
                                   
                                    <td><input type='text' class='inputs' name='csTimeIn' id='csTimeIn'  style='width:100%;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_App["csShiftToIn"]?>'></td>
                                    <td><input type='text' class='inputs' name='csTimeOut' id='csTimeOut'  style='width:100%;'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_App["csHiftToOut"]?>'></td>
                                    <td align="center"><input type="checkbox" name="chkCrossDay" id="chkCrossDay" class="inputs" <?=($arr_App["crossDay"]=='Y'?"checked":"")?>  /></td>
                                  	<td><span class="gridDtlVal">
                               	    <?
										$reasons=$tPopUpObj->getTblData("tblTK_Reasons "," and stat='A' and changeShift='Y'"," order by reason","sqlArres");
										$arrReasons = $tPopUpObj->makeArr($reasons,'reason_id','reason','');
										$tPopUpObj->DropDownMenu($arrReasons,'cmbReasons',$arr_App["csReason"],"class='inputs'");
									?>
                               	    </span></td>
                                    
                                </tr>
                            </table>
                    <?
						}
					?>
                    
                    <?
						if($tableNo==2)
						{
					?>
                    		<table border="1" width="100%" style="border-collapse:collapse;">
                            	<tr style="height:25px;">
                                    <td class="gridDtlLbl" align="center" colspan="2">CHANGE</td>
                                    <td class="gridDtlLbl" align="center" >REMARKS / REASON(S)</td>		
                                   
                                </tr>
                                
                                <tr style="height:20px;">
                                     <td width="27%" class="gridDtlLbl" align="center">FROM</td>		
                                    <td width="27%" class="gridDtlLbl" align="center">TO</td>
                                    <td width="46%" class="gridDtlLbl" align="center"></td>
                                </tr>
                                
                                 <tr>
                    
                                    <td  align="center">
                                        <input tabindex="10" class="inputs" type="text" name="rdDateFrom" readonly="readonly" id="rdDateFrom"  
                                             value="<?=date("Y-m-d", strtotime($arr_App["cRDDateFrom"]))?>" >
                                       
                                    </td>
                                    
                                    <td  align="center">
                                        <input tabindex="10" class="inputs" type="text" name="rdDateTo" readonly="readonly" id="rdDateTo" 
                                             value="<?=date("Y-m-d", strtotime($arr_App["cRDDateTo"]))?>" >
                                                    
                                                        <img src="../../../images/cal_new.png" onClick="displayDatePicker('rdDateTo', this);" style="cursor:pointer;" width="20" height="14">
                                    </td>
                                    <td align="center"><span class="gridDtlVal">
                                    <?
										$reasons=$tPopUpObj->getTblData("tblTK_Reasons "," and stat='A' and changeRestDay='Y'"," order by reason","sqlArres");
										$arrReasons = $tPopUpObj->makeArr($reasons,'reason_id','reason','');
										$tPopUpObj->DropDownMenu($arrReasons,'cmbReasons',$arr_App["cRDReason"],"class='inputs'");
									?>
                                    </span></td>
                                                
                                    
                                </tr>
                            </table>
                    <?
						}
					?>
                    
                    <?
						if($tableNo==3)
						{
					?>
                    <table border="1" width="100%" style="border-collapse:collapse;">
                      <tr>
                        <td width="10%" class="gridDtlLbl" align="center">OB DATE</td>
                        <td width="28%" class="gridDtlLbl" align="center">OB SCHED.</td>
						<td width="4%" class="gridDtlLbl" align="center">CROSS DAY?</td>
                        <td width="16%" class="gridDtlLbl" align="center">DESTINATION</td>
                        <td width="23%" class="gridDtlLbl" align="center">PURPOSE</td>
                        <td width="6%" class="gridDtlLbl" align="center">IN</td>
                        <td width="6%" class="gridDtlLbl" align="center">OUT</td>
                        <td width="5%" class="gridDtlLbl" align="center">CREDIT 8 HRS?</td>
                      </tr>
                      <tr>
                        <td  align="center"><input tabindex="10" class="inputs" type="text" name="obDate" readonly="readonly" id="obDate" size="10" onFocus="getEmpShift(<?=$_GET["empNo"]?>);"
                                             value="<?=date("Y-m-d", strtotime($arr_App["obDate"]))?>" ></td>
                        <td>
						<input type="text" name="schedTimeIn2" id="schedTimeIn2" size="6" class='inputs' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value="<?=$arr_App["obSchedIn"];?>" />
						<span class="gridDtlVal">
                            <?
									  if((float)str_replace(":",".",$arr_App["obSchedIn"])<=12.00){
									  	$skedTIN = "AM";
									  }
									  else{
									  	$skedTIN = "PM"; 	  
									  }
										$tPopUpObj->DropDownMenu(array(''=>'','AM' => 'AM','PM' => 'PM'),'cmbTINAMPM',$skedTIN,'class="inputs"');
									  
									?>
                          </span>
                        <input type="text" name="schedTimeOut2" id="schedTimeOut2" size="6" class='inputs' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value="<?=$arr_App["obSchedOut"];?>" />
						<span class="gridDtlVal">
                            <?
									  if((float)str_replace(":",".",$arr_App["obSchedOut"])<=12.00){
									  	$skedTOUT = "AM";
									  }
									  else{
									  	$skedTOUT = "PM"; 	  
									  }
										$tPopUpObj->DropDownMenu(array(''=>'','AM' => 'AM','PM' => 'PM'),'cmbTOUTAMPM',$skedTOUT,'class="inputs"');
									?>
                          </span>
						</td>
						<td align="center"><input name="crossDay" type="checkbox" <?=($arr_App["crossDay"]=="Y"?"checked":"")?> id="crossDay" /></td>
                        <td><?=$tPopUpObj->DropDownMenu($arrBrnch,'obdestination',$arr_App["obDestination"],'class="inputs"  style="width:100%;"');?></td>
                        <td><?
										$reasons=$tPopUpObj->getTblData("tblTK_Reasons "," and stat='A' and obApp='Y'"," order by reason","sqlArres");
										$arrReasons = $tPopUpObj->makeArr($reasons,'reason_id','reason','');
										$tPopUpObj->DropDownMenu($arrReasons,'obreason',$arr_App["obReason"],'class="inputs"  style="width:100%;"');
									?></td>
                        <td align="center"><input name='txtobTimeIn' type='text' class='inputs' id='txtobTimeIn' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_App["obActualTimeIn"]?>' size="5">
                          </td>
                        <td align="center"><input name='txtobTimeOut' type='text' class='inputs' id='txtobTimeOut' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_App["obActualTimeOut"]?>' size="5">
                          </td>
                        <td align="center"><input type="checkbox" name="rdnDeduct8" <?=($arr_App["hrs8Deduct"]=="Y"?"checked":"")?> id="rdnDeduct8" /></td>
                        
                      </tr>
                    </table>
                    <?
						}
					?>
                    <input type="button" class="inputs" name="btnUpdate" id="btnUpdate" value='UPDATE' onClick="UpdateDetail(<?=$tableNo?>);">
                </td>
            </tr>
        </table>
        </form>
	</BODY>
</HTML>
<SCRIPT>
	
	function UpdateDetail(tableNo)
	{
		var appFields = $('frmPopUp').serialize(true);
		
		if(tableNo=='1')
		{
			if((appFields["csTimeIn"]=="")||(appFields["csTimeIn"]==":"))
			{
				alert("CS Time - In is required.");
				$('csTimeIn').focus();
				return false;
			}
			
			if((appFields["csTimeOut"]=="")||(appFields["csTimeOut"]==":"))
			{
				alert("CS Time - Out is required.");
				$('csTimeOut').focus();
				return false;
			}
			
			if(appFields["csreason"]=="")
			{
				alert("Reason for CS is required.");
				$('csreason').focus();
				return false;
			}
			
			if(appFields["csreason"]=="")
			{
				alert("Reason for CS is required.");
				$('csreason').focus();
				return false;
			}
			
			params = 'transaction_popup.php?action=saveChanges'+tableNo;
		}
		
		if(tableNo=='2')
		{
			if((appFields["rdDateTo"]=="")||(appFields["rdDateTo"]==":"))
			{
				alert("Select rest day to date.");
				$('rdDateTo').focus();
				return false;
			}
			
			if(appFields["rdreason"]=="")
			{
				alert("Reason for RD is required.");
				$('rdreason').focus();
				return false;
			}
			
			params = 'crd.php?action=saveRdSched&Edited=Yes';
		}
		
		if(tableNo=='3')
		{
		var obTimeIN = new Date(appFields["obDate"]+' '+appFields["schedTimeIn2"]+' '+appFields["cmbTINAMPM"]);
		var obTimeOUT = new Date(appFields["obDate"]+' '+appFields["schedTimeOut2"]+' '+appFields["cmbTOUTAMPM"]);
		var obTIN = obTimeIN.getHours();
		var obTOUT = obTimeOUT.getHours();
		
			if(appFields["obreason"]=="")
			{
				alert("Purpose of the OB is required.");
				$('obreason').focus();
				return false;
			}
			
			if(appFields["cmbTINAMPM"]==0)
			{
				alert("SCHED TIME IN AM/PM -  is required.");
				$('cmbTINAMPM').focus();
				return false;
			}

			if(appFields["cmbTOUTAMPM"]==0)
			{
				alert("SCHED TIME OUT AM/PM -  is required.");
				$('cmbTOUTAMPM').focus();
				return false;
			}
				
			if(obTOUT<=obTIN && $('crossDay').checked == false){
				alert("SCHED Time Out should not be less than or equal to SCHED Time In! Please check cross day..");
//				$('cmbTOUTAMPM').focus();
				return false;
			}	
				
			if(appFields["obdestination"]=="")
			{
				alert("Destination of the OB is required.");
				$('obdestination').focus();
				return false;
			}
			
			if((appFields["txtobTimeIn"]=="")||(appFields["txtobTimeIn"]==":"))
			{
				alert("OB Time - In is required.");
				$('txtobTimeIn').focus();
				return false;
			}
			
			if((appFields["txtobTimeOut"]=="") || (appFields["txtobTimeOut"]==":"))
			{
				alert("OB Time - Out is required.");
				$('txtobTimeOut').focus();
				return false;
			}
			
			
			
			params = 'ob.php?action=saveObSched&Edited=Yes';
		}
		
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmPopUp').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			}
		});
		
	}
	
	
</SCRIPT>