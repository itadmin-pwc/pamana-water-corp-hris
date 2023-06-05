<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");


$common = new commonObj();
$pager = new AjaxPager(3,'../../../images/');

$sessionVars = $common->getSeesionVars();

$qryIntMaxRec = "Select * from tblEmp_Educational where empNo='010002428' and compCode='{$_SESSION['company_code']}'";
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$sqlEdu = "SELECT TOP $intLimit  * from tblEmp_Educational where empNo='010002428' and compCode='{$_SESSION['company_code']}'
			       AND id  NOT IN(
			    						 SELECT TOP $intOffset id
										 from tblEmp_Educational where empNo='010002428' and compCode='{$_SESSION['company_code']}')"; 

$resEdu = $common->execQry($sqlEdu);
$arrEdu = $common->getArrRes($resEdu);
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
.style3 {font-size: 10px}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
	<BODY>
		
		<div class="niftyCorner">
        
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" >
				
				<tr>		
			  <td colspan="4" class="parentGridDtl"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><span class="style1">Educational Background</span></td>
                  <td>&nbsp;</td>
      			<td><div align="left">
                                  <div align="left"><span class="parentGridHdr">
                                  <div style="cursor:pointer;" onClick="viewDetails('empEdu_act.php','Add','','empEdu_list_ajax.php','EduDiv',0,0,'txtSrch','cmbSrch')">
                                    <div align="right"><img src="../../../images/application_form_add.png" width="16" border="0" height="16"></div>
                                  </div>
                                  </span></div>
                                </div>
                                </td>
                </tr>
              </table></td>
			  </tr>
				<tr>
					<td>
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="tblPrevEmp" >
							<tr>
							  <td width="14%" class="gridDtlLbl" align="center">Type</td>
								<td width="49%" class="gridDtlLbl" align="center">School</td>
							  <td width="18%" class="gridDtlLbl" align="center">Degree</td>
							  <td width="13%" class="gridDtlLbl" align="center">Date</td>
                                <td width="6%" align="center" class="gridDtlLbl">ACTION</td>
							</tr>
							<? if (count($arrEdu)>0) {
								$i=0;
								foreach($arrEdu as $valEdu) {
								//$arrTotal = $profileobj->getTranDeductionsTotal($sessionVars['compCode'],$rdVal['refNo']);
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr class="rowDtlEmplyrLst">
							  <td class="gridDtlVal style3"><?=$valEdu['type']?></td>
								<td class="gridDtlVal style3"><?=$valEdu['school']?></td>
								<td class="gridDtlVal"><div align="center" class="style3"><font class="gridDtlLblTxt"><?=$valEdu['degree']?>
                                </font></div></td>
								<td class="gridDtlVal"><div align="center" class="style3"><font class="gridDtlLblTxt"><?=date("Y",strtotime($valEdu['dtfrom']))." - " . date("Y",strtotime($valEdu['dtto']));?>
							    </font></div></td>
								
                                <td class="gridDtlVal" align="center"><img onClick="delrestday('<?=$restDayList[$x];?>')"
												src="../../../images/prev_emp_dele.png" width="15" height="15" title="Delete Rest Day" /></td>
						  </tr>
							<? $i++;
								}
							}
							else{
							?>
							<tr>
								<td colspan="5" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
                            <tr>
                            	<td align="center" colspan="5" class="childGridFooter"><? $pager->_viewPagerButton('empEdu_list_ajax.php','EduDiv',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?></td>
                            </tr>
				  </TABLE>				  </td>
				</tr>
			</TABLE>
            
	</div>
		<? $common->disConnect();?>
</BODY>
</HTML>
