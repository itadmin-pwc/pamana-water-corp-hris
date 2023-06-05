<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
$common = new commonObj();

$sqlARList = "SELECT tblARTransData.id,tblARTransData.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpMast.empStat, tblLoanType.lonTypeShortDesc as loanType, 
 				tblARTransData.refNo,tblARTransData.amount, tblARTransData.dedAmt, tblARTransData.dedSked, tblARTransData.NoDed, tblARTransData.transDate, tblEmpLoans.lonRefNo
		FROM tblARTransData INNER JOIN
                      tblLoanType ON tblARTransData.transType = tblLoanType.lonTypeCd LEFT OUTER JOIN
                      tblEmpLoans ON tblARTransData.transType = tblEmpLoans.lonTypeCd AND 
                      tblARTransData.refNo  = tblEmpLoans.lonRefNo LEFT OUTER JOIN
                      tblEmpMast ON tblARTransData.empno  = tblEmpMast.empNo and empStat IN ('RG','CN','PR')
		WHERE userID='{$_SESSION['user_id']}' and status is null
ORDER BY tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, refNo";

$sqlARList = $common->execQry($sqlARList);
$arrARList = $common->getArrRes($sqlARList);

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<script src="../../../js/extjs/adapter/prototype/scriptaculous.js" type="text/javascript"></script>
		<script src="../../../js/extjs/adapter/prototype/unittest.js" type="text/javascript"></script>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<SCRIPT type="text/javascript" src="timesheet_js.js"></SCRIPT>		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		
		

		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>		
	</HEAD>
	<BODY>
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	  <tr>
	    <td colspan="4" class="parentGridHdr">&nbsp;<img src="../../../images/grid.png">&nbsp;AR List</td>
      </tr>
	  <tr>
	    <td colspan="4">
       <form action="" method="post" name="frmAR" id="frmAR">
<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
	      <tr>
	        <td colspan="8" class="gridToolbar">
            <input class="inputs" type="button" name="btnSrch" id="btnSrch" value="Load to Payroll" onClick="LoadtoPayroll()" />
            </td>
          </tr>
	      <tr>
	        <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" id="ChkAll" name="ChkAll" onClick="ToggleAll()"></td>
	        <td width="11%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
	        <td width="23%" class="gridDtlLbl" align="center">NAME</td>
	        <td width="12%" class="gridDtlLbl" align="center">LOAN TYPE</td>
	        <td width="17%" class="gridDtlLbl" align="center">REF. NO</td>
	        <td width="12%" class="gridDtlLbl" align="center">AMOUNT</td>
	        <td width="12%" class="gridDtlLbl" align="center">DEDUCTION</td>
	        <td width="11%" class="gridDtlLbl" align="center">NO. DED</td>
          </tr>
	      <? 
					if($common->getRecCount($sqlARList) > 0){
						$i=0;
						foreach ($arrARList as $val){
							$hdvalue = (trim($val['empNo']) == "" || $val['lonRefNo'] != "" ) ? 0:1;
							$chDis = (trim($val['empNo']) == "" || $val['lonRefNo'] != "" ) ? "disabled style='visibility:hidden; height:1px; width:1px'":"";
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
	      <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
	        <td class="gridDtlVal">
            <? if ($chDis != "") {?><div align="center">
            	<img src="../../../images/edit_prev_emp.png" align="middle" onClick="maintBranch('<?=$val['id']?>');" style="cursor:pointer;"></div>
            <? } else {?>
            <input type="checkbox" <?=$chDis?> onClick="Toggle(this.id)" value="<?=$val['id']?>" id="ch_<?=$i?>" name="ch_<?=$i?>">
            <input type="hidden" value="<?=$hdvalue?>" id="hd_<?=$i?>"   name="hd_<?=$i?>" >
           <? } ?>
            </td>
	        <td class="gridDtlVal"><font class="gridDtlLblTxt">
	          <?
			  echo $val['empNo'];
			  if ($chDis != "") {
			  ?>
	        <input type="checkbox" <?=$chDis?> onClick="Toggle(this.id)" value="<?=$val['id']?>" id="ch_<?=$i?>" name="ch_<?=$i?>">
            <input type="hidden" value="<?=$hdvalue?>" id="hd_<?=$i?>"   name="hd_<?=$i?>" >
            <? } ?>
              </font></td>
	        <td class="gridDtlVal"><font class="gridDtlLblTxt">
	          <?
			  if (trim($val['empNo']) != "")
				  echo str_replace("Ñ","&Ntilde;",htmlentities($val['empLastName']). ", " . htmlentities($val['empFirstName']) ." ". htmlentities($val['empMidName'][0])) . ".";?>
	          </font></td>
	        <td class="gridDtlVal"><font class="gridDtlLblTxt">
	          <?=$val['loanType']?>
	          </font></td>
	        <td class="gridDtlVal"><?=$val['refNo']?></td>
	        <td class="gridDtlVal"><?=number_format($val['amount'],2)?></td>
	        <td class="gridDtlVal"><?=number_format($val['dedAmt'],2)?></td>
	        <td class="gridDtlVal"><?=$val['NoDed']?></td>
          </tr>

	      <?
						}
					}
					else{
					?>
	      <tr>
	        <td colspan="8" align="center"><font class="zeroMsg">NOTHING TO DISPLAY</font></td>
          </tr>
	      <?}?>
	      </table><input type="hidden" id="hdnCtr" value="<?=$i?>" name="hdnCtr"> 
        <input type="hidden" id="loadID" value="" name="loadID">         
        </form></td>
      </tr>
	  <tr>
	    <td colspan="4" height="25"></td>
      </tr>
    </TABLE>
	</BODY>
</HTML>
<SCRIPT>
	
	
	
	function ToggleAll() {
		var cnt = $('hdnCtr').value;
		var str = "";
		for(i=1;i<=cnt;i++){
			if ($('ChkAll').checked==false) {
				$('ch_'+i).checked = false;
			} else {
				if ($('hd_'+i).value == 1) {
					$('ch_'+i).checked = true;
					str = str + "," + $('ch_'+i).value;
				}
			}
		}
		$('loadID').value = str;
	}
	function Toggle(id) {
		var str = $('loadID').value;
		if ($(id).checked==false) {
			str = str.replace(","+$(id).value,"");
		} else {
			str = str +","+$(id).value;
		}
		$('loadID').value = str;
	}

	function getSelectedAR() {
		var cnt = $('hdnCtr').value;
		var ctr = 0;
		var val = "";
		for(i=1;i<=cnt;i++){
			if ($('ch_'+i).checked==true) {
				val = $('ch_'+i).value;
				ctr++;
			}
		}
		if (ctr == 1) {
			return val;	
		} else if(ctr > 1) {
			return -1;	
		}
	}
	
	function maintBranch(id){
			
		var editBrnch = new Window({
		id: "editBrnch",
		className : 'mac_os_x',
		width:430, 
		height:170, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: " EDIT", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editBrnch.setURL('ar_act.php?act=Edit&id='+id);
		editBrnch.show(true);
		editBrnch.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editBrnch) {
		        Windows.removeObserver(this);
				location.href='ar_list.php';
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		
	}	


	function LoadtoPayroll() {
		var empInputs = $('frmAR').serialize(true);
		if (empInputs['loadID'] == "") {
			alert('Nothing to Load.');
            return false;		
		} 
		params = 'ar_act.php?act=LoadtoPayroll';
		new Ajax.Request(params,{
			method : 'post',
			parameters : $('frmAR').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</SCRIPT>