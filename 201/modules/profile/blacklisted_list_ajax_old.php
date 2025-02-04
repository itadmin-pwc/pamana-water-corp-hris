<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/24/2010
	Function		:	Blacklist Module (Main View Ajax) 
*/

session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("blacklisted_obj.php");

$common = new commonObj();
$blackListObj = new blackListObj();
$sessionVars = $blackListObj->getSeesionVars();
$blackListObj->validateSessions('','MODULES');

if ($_SESSION['user_level'] == 3) 
{
	$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
	
	if($userinfo['empLocCode']=='0001')
	{
		$brnCode = " $and empLocCode='".$userinfo['empLocCode']."'";
		$brnCodelist = " AND  empLocCode='".$userinfo['empLocCode']."'";
	}
	else
	{
		$brnCode = " $and empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
		$brnCodelist = " AND empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
		$brnCode_View = 1;
	}
}
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER','SSS. NO');

if($brnCode_View =="")
{
	$arrBrnch = $common->makeArr($common->getAllBranch(),'brnCode','brnDesc','All');
}

$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE  
				 empPayCat<>0
				 $brnCodelist
				";
				
if($_GET['isSearch'] == 1)
{
	if($_GET['srchType'] == 2){
		$qryIntMaxRec .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
	}
	if($_GET['srchType'] == 0){
		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}
	if($_GET['srchType'] == 1){
		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}
	if($_GET['srchType'] == 3){
		$qryIntMaxRec .= "AND empSssNo LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}
	
	if ($_GET['brnCd']!=0) 
	{
		$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
	}
}
//echo $qryIntMaxRec;
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT TOP $intLimit *
				FROM tblEmpMast
				WHERE 
				empPayCat<>0 and
				empNo NOT IN
				(SELECT TOP $intOffset empNo FROM tblEmpMast WHERE empPayCat<>0 $brnCodelist "; 

if($_GET['isSearch'] == 1)
{
	if($_GET['srchType'] == 2){
		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
	}
	if($_GET['srchType'] == 0){
		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}
	if($_GET['srchType'] == 1){
		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}
	
	if($_GET['srchType'] == 3){
		$qryEmpList .= "AND empSssNo LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}
	
	if ($_GET['brnCd']!=0) 
	{
		$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
	}
}  

$qryEmpList .= " ORDER BY empLastName) 
				 $brnCodelist";

if($_GET['isSearch'] == 1)
{
	if($_GET['srchType'] == 0){
		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
	}
	if($_GET['srchType'] == 1){
		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}
	if($_GET['srchType'] == 2){
		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}
	
	if($_GET['srchType'] == 3){
		$qryEmpList.= "AND empSssNo LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}
	
	if ($_GET['brnCd']!=0) 
	{
		$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
	}
}
$qryEmpList .=	"ORDER BY empLastName";
//echo $qryEmpList;
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);


		
?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;BlackListed Employees
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="7" class="gridToolbar">
						<FONT class="ToolBarseparator">&nbsp;</font>
						<?
							if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh')
							{
								if(isset($_GET['srchType']) )
								{ 
									$srchType = $_GET['srchType'];
								}
							}
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET["srchType"],'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('blacklisted_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
					</td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="50%" class="gridDtlLbl" align="center">NAME</td>
                        <td width="20%" class="gridDtlLbl" align="center">REMARKS</td>
						<td class="gridDtlLbl" align="center">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0)
					{
						$i=0;
						foreach ($arrEmpList as $empListVal)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							
							//Check the Employee if he/she has record in tblBlacklistedEmp
							$res_noOfRecords = $blackListObj->chkEmpNoRecords($empListVal['empNo']);
							$noOfRecords = $blackListObj->getRecCount($res_noOfRecords);
							if($noOfRecords>=1)
								$visibility = "";
							else
								$visibility = "hidden";
					?>
					
                    <tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
                        <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("�","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
						<td class="gridDtlVal"><font color="#FF0000" class="gridDtlLblTxt"><?php echo ($visibility!=""?"":"BLACKLISTED"); ?></font></td>
						<td class="gridDtlVal" >
							<table border="0" width="70%" align="center">
                            	<tr align="center" >
                                	
                                	
                                	<?php if ($_SESSION['user_level'] != 3) 
									{
									?>     
                                    	<td style="visibility:<?php echo $visibility; ?>">
                                            <a href="#"  onClick="blackList_Pop('<?=$empListVal['empNo']?>','v');"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="View BlackList Information"></a>                                    
                                       </td>
                                        
                                    	<td style="visibility:<?php echo $visibility; ?>">
                                            <a href="#"  onClick="blackList_Pop('<?=$empListVal['empNo']?>','e');"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Employee BlackList Information"></a>                                    
                                        </td>
                                        
                                        <td class="gridDtlVal" align="left">
                                            <a href="#" onClick="blackList_Pop('<?=$empListVal['empNo']?>','a');"><img class="toolbarImg" src="../../../images/application_form_add.png" title="Set the Employee as BlackListed."></a>                                    
                                        </td>
                                     
                                    <?php 
									} 
									?>
                                </tr>
                            </table>
                        </td>
					</tr>
					
					<?
						}
					}
					else
					{
					?>
					<tr>
						<td colspan="7" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?
                    }
					?>
					<tr>
						<td colspan="7" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("blacklisted_list_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
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