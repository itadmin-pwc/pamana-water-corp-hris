<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

$qryIntMaxRec = "SELECT allw.compCode,allw.empNo,allw.allowCode,allw.allowAmt,allw.allowSked,
						allw.allowTaxTag,allw.allowPayTag,allw.allowStart,allw.allowEnd,allw.allowStat,allwTyp.allowDesc
				FROM tblAllowance as allw LEFT JOIN tblAllowType as allwTyp 
				ON allwTyp.compCode = allw.compCode AND allwTyp.allowCode = allw.allowCode
				
			    WHERE allw.compCode = '{$sessionVars['compCode']}'
			    AND allw.empNo = '{$_GET['empNo']}' ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND allw.allowCode LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND allwTyp.allowDesc LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        }
        $qryIntMaxRec .= "ORDER BY allwTyp.allowDesc ";
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpAllwList = "SELECT TOP $intLimit allw.compCode,allw.empNo,allw.allowCode,allw.allowAmt,allw.allowSked,
						                allw.allowTaxTag,allw.allowPayTag,allw.allowStart,allw.allowEnd,allw.allowStat,
						                allwTyp.allowDesc
				   FROM tblAllowance as allw LEFT JOIN tblAllowType as allwTyp 
				   ON allwTyp.compCode = allw.compCode AND allwTyp.allowCode = allw.allowCode
			       WHERE allw.compCode = '{$sessionVars['compCode']}'
			       AND allw.empNo = '{$_GET['empNo']}'
			       AND allw.allowCode NOT IN(
			    						 SELECT TOP $intOffset allw.allowCode
										 FROM tblAllowance as allw LEFT JOIN tblAllowType as allwTyp 
										 ON allwTyp.compCode = allw.compCode AND allwTyp.allowCode = allw.allowCode
									     WHERE allw.compCode = '{$sessionVars['compCode']}'
									     AND allw.empNo = '{$_GET['empNo']}' "; 
		        if($_GET['isSearch'] == 1){
		        	if($_GET['srchType'] == 0){
		        		$qryEmpAllwList .= "AND allw.allowCode LIKE '".trim($_GET['txtSrch'])."%' ";
		        	}
		        	if($_GET['srchType'] == 1){
		        		$qryEmpAllwList .= "AND allwTyp.allowDesc LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
		        	}
		        }  
$qryEmpAllwList .= "ORDER BY allwTyp.allowDesc) ";
		        if($_GET['isSearch'] == 1){
		        	if($_GET['srchType'] == 0){
		        		$qryEmpAllwList .= "AND allw.allowCode LIKE '".trim($_GET['txtSrch'])."%' ";
		        	}
		        	if($_GET['srchType'] == 1){
		        		$qryEmpAllwList .= "AND allwTyp.allowDesc LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
		        	}
		        }
$qryEmpAllwList .=	"ORDER BY allwTyp.allowDesc ";

$resEmpAllwList = $common->execQry($qryEmpAllwList);
$arrEmpAllwList = $common->getArrRes($resEmpAllwList);

$userInfo = $common->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');
?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Allowance List (<?=$userInfo['empNo'] ." - " . $userInfo['empFirstName'] . " " . $userInfo['empMidName'] . " " . $userInfo['empLastName']?>) (<?=($userInfo['empPayType']=='M') ? 'Monthly' : 'Daily';?>)
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="10" class="gridToolbar">
						<a href="#"  class="anchor" onclick="maintAllow('Add','<?=$_GET['empNo']?>','','employee_Allowance_listAjaxResult.php','empAllowList','<?=$empAllwListVal['allowCode']?>',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add Allowance</a> 
                        <FONT class="ToolBarseparator">|</font>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$common->DropDownMenu(array('ALLOWANCE CODE','ALLOWANCE DESCRIPTION'),'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('employee_allowance_listAjaxResult.php','empAllowList','Search',0,1,'txtSrch','cmbSrch','&empNo=<?=$_GET['empNo']?>','../../../images/')">
						<INPUT type="button" name="btnBack" id="btnBack" value="BACK" onclick="location.href='profile_list.php'" class="inputs">
					</td>
					<tr>
						<td class="gridDtlLbl" align="center" width="2%">#</td>
						<td class="gridDtlLbl" align="center" width="20%">CODE-TYPE</td>
						<td  class="gridDtlLbl" align="center" width="10%">AMOUNT</td>
						<td  class="gridDtlLbl" align="center" width="18%">SCHEDULE</td>
						<!--<td  class="gridDtlLbl" align="center" width="10%">TAX TAG</td>-->
						<td  class="gridDtlLbl" align="center" width="10%">PAY TAG</td>
						<td  class="gridDtlLbl" align="center" width="12%">START DATE</td>
						<td  class="gridDtlLbl" align="center" width="12%">END DATE</td>
						
						<td  class="gridDtlLbl" align="center" width="15%">STATUS</td>
						<td class="gridDtlLbl" align="center">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpAllwList) > 0){
						$i=0;
						foreach ($arrEmpAllwList as $empAllwListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empAllwListVal['allowCode']."-".$empAllwListVal['allowDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empAllwListVal['allowAmt']?></font></td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if($empAllwListVal['allowSked'] == 1){
										echo "1st Payroll of Month";
									}
									if($empAllwListVal['allowSked'] == 2){
										echo "2nd Payroll of Month";
									}
									if($empAllwListVal['allowSked'] == 3){
										echo "Both Payrolls";
									}
								?>
							</font>
						</td>
<!--						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<? 
									if($empAllwListVal['allowTaxTag'] == 'Y'){
										echo "Taxable";
									}
									else if($empAllwListVal['allowTaxTag'] == 'N'){
										echo "Not Taxable";
									}
									else{
										echo "---";
									}
								?>
							</font>
						</td>-->
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if($empAllwListVal['allowPayTag'] == 'P'){
										echo "Permanent";
									}
									else{
										echo "Temporary";
									}
								?>
							</font>
						</td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if(date('m/d/Y',strtotime($empAllwListVal['allowStart'])) == '01/01/1970'){
										echo "---";				
									}
									else{
										echo date('m/d/Y',strtotime($empAllwListVal['allowStart']));
									}
								?>
							</font>
						</td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if(date('m/d/Y',strtotime($empAllwListVal['allowEnd'])) == '01/01/1970'){
										echo "---";				
									}
									else{
										echo date('m/d/Y',strtotime($empAllwListVal['allowEnd']));
									}
								?>
							</font>
						</td>
						
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if($empAllwListVal['allowStat'] == 'A'){
										echo "Active";		
									}
									else{
										echo "Held";		
									}
								?>
							</font>
						</td>
						<td class="gridDtlVal" align="center">

							<a href="#" onclick="maintAllow('Edit','<?=$empAllwListVal['empNo']?>','<?=$empAllwListVal['allowCode']?>','employee_Allowance_listAjaxResult.php','empAllowList','<?=$empAllwListVal['allowCode']?>',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit"></a>
							<a href="#"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete" onclick="deleEmpAllw('employee_Allowance_listAjaxResult.php','empAllowList','<?=$empAllwListVal['empNo']?>','<?=$empAllwListVal['allowCode']?>',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch','<?php echo htmlspecialchars(addslashes($empAllwListVal['allowDesc']))?>')"></a>
						</td>
					</tr>
					<?
						}
					}
					else{						
					?>
					<tr>
						<td colspan="10" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="10" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton('employee_Allowance_listAjaxResult.php','empAllowList',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$_GET['empNo']);?>
						</td>
					</tr>
				</TABLE>
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>