<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$profileobj = new commonObj();
//$sessionVars = $profileobj->getSeesionVars();
//$profileobj->validateSessions('','MODULES');

$pager = new AjaxPager(8,'../../../images/');

if ($_SESSION['strprofile']=="") {
	$_SESSION['strprofile']=$profileobj->createstrwil();
} else { 
	$compCode = "AND compCode = '{$_SESSION['oldcompCode']}'";
}

$qryIntMaxRec = "SELECT tblContactTypeRef.contactDesc, tblContactMast.contactName, tblContactMast.recNo FROM tblContactMast INNER JOIN tblContactTypeRef ON tblContactMast.contactCd = tblContactTypeRef.contactCd where empNo='{$_SESSION['strprofile']}' ORDER BY recNo";
$resIntMaxRec = $profileobj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;	
$qryDedList = "SELECT *
		FROM tblContactMast INNER JOIN tblContactTypeRef ON tblContactMast.contactCd = tblContactTypeRef.contactCd
		WHERE 0=0 AND empNo = '{$_SESSION['strprofile']}'
				$compCode
			    ORDER BY recNo  limit $intOffset,$intLimit";

$resDedList = $profileobj->execQry($qryDedList);
$arrDedList = $profileobj->getArrRes($resDedList);
?>

<HTML>
<head>
<style type="text/css">
<!--
.style1 {
	font-family: verdana;
	font-size: 11px;
	font-weight: bold;
}
.style2 {font-size: 8px}
-->
</style>
</head>
	<BODY>
		
		<div class="niftyCorner">
        
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" >
				
				<tr>		
			  <td colspan="4" class="parentGridDtl"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><span class="style1">CONTACT LIST</span></td>
                  <td><span style="visibility:hidden;" id="loadrefresh" ><span class="style2">Search</span>
                  <INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" style="height:8px;">
                    <span class="style2">In
                    <?=$profileobj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
                    </span>
  <INPUT  style="height:8px;" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('contact_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
                  </span></td>
                  <td><?
                                //if ($_SESSION['profile_act']!="View") {
								?>
                                <div align="left"><span class="parentGridHdr"><div style="cursor:pointer;" onClick="viewDetails('contact_act.php','Add','<?=$dedListVal['recNo']?>','contact_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                                  <div align="right"><img src="../../../images/application_form_add.png" width="16" border="0" height="16"></div>
                                </div>
                                </span></div>
                                <? //} ?>
                  </td>
                </tr>
              </table></td>
			  </tr>
				<tr>
					<td>
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="tblPrevEmp" >
							<tr>
								<td width="30%" class="gridDtlLbl" align="center">Contact Type</td>
								<td width="47%" class="gridDtlLbl" align="center">Contact</td>
                               <?
                                if ($_SESSION['profile_act']!="View") {
								?>
								<td width="23%" align="center" class="gridDtlLbl">ACTION</td>
                                <? }?>
							</tr>
							<?
							if($profileobj->getRecCount($resDedList) > 0){
								$i=0;
								foreach ($arrDedList as $dedListVal){
								//$arrTotal = $profileobj->getTranDeductionsTotal($sessionVars['compCode'],$dedListVal['refNo']);
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr class="rowDtlEmplyrLst">
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$dedListVal['contactDesc']?></font></td>
								<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$dedListVal['contactName']?></font></td>
                                                                <?
                                //if ($_SESSION['profile_act']!="View") {
								?>
								<td class="gridDtlVal" align="center"> 
									<span class="gridDtlVal"><a href="#" onClick="viewDetails('contact_act.php','Edit','<?=$dedListVal['recNo']?>','contact_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_edit.png" border="0" class="actionImg" title="Edit Contact" /></a> <img onClick="deleContact('<?=$dedListVal['recNo']?>','contact_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch','<?=htmlspecialchars(addslashes($dedListVal['contactName']));?>')"
												src="../../../images/application_form_delete.png" width="15" height="15" title="Delete Contact" /> </span></td>
                                    <? //} ?>            
						  </tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="3" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
				  </TABLE>				  </td>
				</tr>
			</TABLE>
     <span class="childGridFooter"><? $pager->_viewPagerButton("contact_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch',"&groupType=".$groupType."&catType=".$catType);?></span>       
	</div>
		<? $profileobj->disConnect();?>
		<form name="frmTSko" method="post">
		  <input type="hidden" name="txtSrch2" id="txtSrch2" value="<?php echo $_GET['txtSrch']; ?>">
			<input type="hidden" name="isSearch2" id="isSearch2" value="<?php echo $_GET['isSearch']; ?>">
			<input type="hidden" name="srchType2" id="srchType2" value="<?php echo $_GET['srchType']; ?>">
		</form>
	</BODY>
</HTML>
