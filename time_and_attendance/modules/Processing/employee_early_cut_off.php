<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("employee_early_cut_off.obj.php");

$emptimesheetObj = new earlyObj();
$sessionVars = $emptimesheetObj->getSeesionVars();

switch($_GET["action"])
{
	case 'saveEarlyCut':
		if($emptimesheetObj->updateEarlyCut($_GET['csDateFrom'], $_GET['csDateTo'])) {
			echo "alert('Early Cut-off successfully executed.');";
		}else{
			echo "alert('Error executing early cut-off.');";
		}
	break;
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
      
        <SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url('../../../js/themes/alphacube.css');</STYLE>	

		<SCRIPT type="text/javascript" src="../../../includes/calendar.js"></SCRIPT>
        
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>
        
		<script type="text/javascript">
			function cnclLockSys(){
				Windows.getWindow('winLcok').close();
				$('passLock').style.visibility = 'hidden';
			}	
			function Dolock(){
				var winLock = new Window({
					id : "winLcok",
					className: "alphacube", 
					resizable: false, 
					draggable:false, 
					minimizable : false,
					maximizable : false,
					closable 	: false,
					width: 200,
					height : 80
				});
				$('passLock').style.visibility = 'visible';
				winLock.setContent('passLock', false, false);				
				winLock.setZIndex(500);
				winLock.setDestroyOnClose();
				winLock.showCenter(true);				
			}
		</script>
	</HEAD>
	<BODY>
		<FORM name='frmCS' id="frmCS" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        	
		<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
			<tr>
				<td colspan="4" class="parentGridHdr">
					&nbsp;<img src="../../../images/grid.png">&nbsp;Early Cut-Off
				</td>
			</tr>
			
			<tr>
				<td class="parentGridDtl" valign="top">
					
					<TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
						<tr style="height:25px;">
							<td class="gridDtlLbl" align="center">FROM</td>
							<td class="gridDtlLbl" align="center">TO</td>
							<!--<td class="gridDtlLbl" align="center" >PAYROLL PERIOD COVERED</td>	-->
							<td  class="gridDtlLbl" align="center">ACTION</td>
						</tr>
						
						<tr>
							<td  align="center">
								<?php
								$empNo = $_GET['empNo'] == '' ? $_SESSION['employeenumber'] : $_GET['empNo'];
								?>
								<input tabindex="10" class="inputs" type="text" name="csDateFrom" readonly="readonly" id="csDateFrom" size="10"
									value="<? 	
												$format="Y-m-d";
												$strf=date($format);
												echo("$strf"); 
											?>" >
											
												<img src="../../../images/cal_new.png" onClick="displayDatePicker('csDateFrom', this);" style="cursor:pointer;" width="20" height="14">
							
							</td>
						
							<td  align="center">
								<input tabindex="10" class="inputs" type="text" name="csDateTo" readonly="readonly" id="csDateTo" size="10"
									value="<? 	
												$format="Y-m-d";
												$strf=date($format);
												echo("$strf"); 
											?>" >
											<img src="../../../images/cal_new.png" onClick="displayDatePicker('csDateTo', this);" style="cursor:pointer;" width="20" height="14">
							</td>
						
						<!-- <td>
								<?php
									
									//$arrPayPd = $csObj->makeArr($csObj->getPeriodGtOpnPer($_SESSION["company_code"],$_GET["empPayGrp"],$_GET['empPayCat'],$_GET["payPayable"]),'pdSeries','pdPayable','');
									//$csObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis.'class="inputs" style="width:100%;" ');
								?>
							
							</td>-->
							<td align="center"><input type="button" class="inputs" name="btnSave" id="btnSave" value='Execute' onClick="saveCsDetail();"></td>
						</tr>
						
						
					</TABLE>	
				</td>
			</tr>
		</TABLE>
           
			<div id="indicator1" align="center"></div>
            
             <div id="passLock" style="visibility:hidden;" >
                <TABLE align="center" border="0" width="100%">
                    
                    <TR>
                      <td align="center"><img src="../../../images/loading.gif" width="120" height="40"></td>
                  </TR>
                    <TR>
                        <td align="center">
                            <font class='cnfrmLbl style6'><strong>Saving</strong></font></td>
                    </TR>
                </TABLE>			
            </div>  
            
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	function saveCsDetail()
	{
		var csFields = $('frmCS').serialize(true);
		
		if(csFields["csDateFrom"]=="")
		{
			alert("Select Date From first.");
			$('csDateFrom').focus();
			return false;
		}

		if(csFields["csDateTo"]=="")
		{
			alert("Select Date From first.");
			$('csDateTo').focus();
			return false;
		}
		
		var conUser = confirm("Are you sure to execute early cut-off?");

		if(conUser==true)
		{
			params = 'employee_early_cut_off.php?action=saveEarlyCut';
						
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmCS').serialize(),
				onComplete : function (req){
					eval(req.responseText);
					//pager('csAjaxResult.php','csCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function(){
					Dolock();
				},
				onSuccess: function (){
					cnclLockSys();
				}	
			});
		}
	}
	
	function getSeqNo()
	{
		var param = '?action=getSeqNo&seqNo=';
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmCS').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}
	
	function saveMessage(msg)
	{
		alert(msg);
		document.frmCS.csDateTo.value="";
		document.frmCS.csDateFrom.value="";
	
	}
</SCRIPT>