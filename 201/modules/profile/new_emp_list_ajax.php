<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
if ($_SESSION['user_level'] == 3) {
	$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		   $brnCodelist = " AND empNo<>'".$_SESSION['employee_number']."' and empbrnCode IN (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
} 
elseif ($_SESSION['user_level'] == 2) {
	$brnCodelist = " AND empbrnCode IN (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
}

$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');

if($brnCode_View ==""){
	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblUserBranch tblUB, tblBranch as tblbrn
						where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
						and empNo='".$_SESSION['employee_number']."'
						order by brnDesc";
	
	$resBrnches = $common->execQry($queryBrnches);
	$arrBrnches = $common->getArrRes($resBrnches);
	$arrBrnch = $common->makeArr($arrBrnches,'brnCode','brnDesc','All');
}


$qryIntMaxRec = "SELECT * FROM tblEmpMast_new 
			     WHERE compCode = '{$sessionVars['compCode']}' 
				 $brnCodelist
				 AND (stat='H' or stat is null)
				 AND empPayCat<>0 ";
				
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT *
		FROM tblEmpMast_new
		WHERE  empPayCat<>0 
		AND (stat='H' or stat is null)
		AND compCode = '{$sessionVars['compCode']}'  $brnCodelist "; 
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;		
$qryEmpList .=	"ORDER BY empLastName limit $intOffset,$intLimit";

$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);
?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;New Employee  List</td>
	  </tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
						<?
                        if($_SESSION['user_level']!=3){
						?>
                        <a href="#" onclick="location.href='new_emp_profile.php?act=Add'" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Employee</a> |
                        <?
                        }
						?>
                         <FONT class="ToolBarseparator">&nbsp;</font>
						<?
					/*	if($_GET['action']=='Search'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}*/
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							echo 'wil';
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('new_emp_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
					
                    </td>
					<tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="21%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="37%" class="gridDtlLbl" align="center">NAME</td>
						<td width="34%" class="gridDtlLbl" align="center">BRANCH</td>
						<td width="34%" class="gridDtlLbl" align="center">W/ Salary</td>
						<td width="6%" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
						  <?=str_replace("&Ntilde;","&Ntilde;",htmlentities($empListVal['empLastName']). ", " . htmlentities($empListVal['empFirstName']) ." ". htmlentities($empListVal['empMidName']));?>
						</font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
						  <?= $brnch['brnDesc'] = $common->getInfoBranch($empListVal['empBrnCode'],$empListVal['compCode']);?>
						</font></td>
						<td class="gridDtlVal"><center>
							<?php
								if($empListVal['empMrate'] > 0 || $empListVal['empDrate'] > 0) {
									echo '<font color="green">OK</font>';
								}else{
									echo '<font color="red">X</font>';
								}
							?></center>
						</td>
                        
                        
                        <td class="gridDtlVal" >
							<table border="0" width="70%" align="center">
                            	<tr align="center" >
							                                	
									<td><a href="#" onClick="location.href='new_emp_profile.php?act=Edit&empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Employee Information"></a>                                    </td>
                                    <td><img src="../../../images/application_form_delete.png" onclick="DelNewEmp('<?=$empListVal['compCode']?>','<?=$empListVal['empNo']?>','<?=$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName']?>');" style="cursor:pointer;" width="16" height="16" title="Delete Employee Informaion" /></td>
                                           
                              </tr>
                            </table>
                      </td>
					</tr>
					
					<?
						}
					}
					else{
					?>
					<tr>
						<td colspan="8" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("new_emp_list_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>

<?$common->disConnect();?>
<form name="frmTS" method="post">
<input type="hidden" name="brnCd" id="brnCd" value="<?php echo $_GET['brnCd']; ?>">
</form>