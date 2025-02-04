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
	
	if(($userinfo['empLocCode']=='999')&&($_SESSION['employee_number']=='120001521')){
		$brnCode = " $and empLocCode='".$userinfo['empLocCode']."'";
		$brnCodelist = " AND  empLocCode='".$userinfo['empLocCode']."'";
	}
	else{
		if($userinfo['empLocCode']!='999'){
			$brnCode = " $and empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCodelist = " AND empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCode_View = 1;
		}else{
			$brnCode = " $and empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empBrnCode']."'";
			$brnCodelist = " AND empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empBrnCode']."'";
			$brnCode_View = 1;
		}
	}
}
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');

if($brnCode_View ==""){
	$arrBrnch = $common->makeArr($common->getBrnchArt($_SESSION["company_code"]),'brnCode','brnDesc','All');
}


$qryIntMaxRec = "SELECT * FROM tblBlacklistedEmp 
			     ";
				
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "WHERE Emp_ID LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "WHERE Emp_last LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "WHERE Emp_first LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			
        }

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT TOP $intLimit *
		FROM tblBlacklistedEmp
		WHERE 
		Blacklist_no NOT IN
        (SELECT TOP $intOffset Blacklist_no FROM tblBlacklistedEmp "; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 2){
				$qryEmpList .= " WHERE Emp_ID LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= " WHERE Emp_last LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= " WHERE Emp_first LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			
        }  
$qryEmpList .= " ORDER BY Emp_last)";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 2){
				$qryEmpList .= " AND Emp_ID LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= " AND  Emp_last LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= " AND  Emp_first LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }
 $qryEmpList .=	"ORDER BY Emp_last";

$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Blacklisted Employees</td>
	  </tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					  <td colspan="7" class="gridToolbar">
						<?php if ($_SESSION['user_level'] != 3) {						?>
                        <a href="#" onclick="location.href='profile.php?act=Add'" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Employee</a> |
						<?php } ?>
                         <FONT class="ToolBarseparator">&nbsp;</font>
						<?
					/*	if($_GET['action']=='Search'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}*/
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search
						<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$srchType,'class="inputs"');?>
						
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('blacklisted_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','','','../../../images/')">
					
                    </td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="70%" class="gridDtlLbl" align="center">NAME</td>
						<td class="gridDtlLbl" align="center">ACTION</td>
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
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['Emp_ID']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("�","&Ntilde;",$empListVal['Emp_last']. ", " . $empListVal['Emp_first'] ." ". $empListVal['Emp_middle'])?></font></td>
						
                        
                        
                        <td class="gridDtlVal" >
							<table border="0" width="70%" align="center">
                            	<tr align="center" >
                               	  <?php if ($_SESSION['user_level'] != 3) {?>                        
									<td>
                                    	<a href="#" onClick="location.href='profile.php?act=Edit&Emp_ID=<?=$empListVal['Emp_ID']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Employee Information"></a>                                    </td>
                                    
                                    <td class="gridDtlVal" align="left">
                                            <a href="#" onClick="location.href='blacklisted_actionlist.php?Emp_ID=<?=$empListVal['Emp_ID']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="PAF"></a>                                    </td>
                      <?php } ?>
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
						<td colspan="7" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="7" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("blacklisted_list_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','','');?>
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