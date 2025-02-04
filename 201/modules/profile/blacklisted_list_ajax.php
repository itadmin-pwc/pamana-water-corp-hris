<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/24/2010
	Function		:	Blacklist Module (Main View Ajax) 
*/

session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("blacklisted_obj.php");

$common = new commonObj();
$blackListObj = new blackListObj();
$sessionVars = $blackListObj->getSeesionVars();
$blackListObj->validateSessions('','MODULES');

$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER','SSS. NO','BRANCH');

//$qryIntMaxRec = "CALL sp_BlacklistedEmp_Max ('{$_GET['isSearch']}','{$_GET['srchType']}','{$_GET['txtSrch']}')";
$qryIntMaxRec = "SELECT * FROM tblblacklistedemp where 0=0 ";
if($_GET['isSearch'] == 1)
{
	if($_GET['srchType'] == 0){
		$qryIntMaxRec .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
	}
	if($_GET['srchType'] == 1){
		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}
	if($_GET['srchType'] == 2){
		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}
	
	if($_GET['srchType'] == 3){
		$qryIntMaxRec .= "AND empSssNo LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}
	
	if($_GET['srchType'] == 4){
		$qryIntMaxRec .= "AND empBrnCode LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}	
}


$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);


$qryEmpList = "SELECT * FROM tblblacklistedemp where 0=0 ";

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
		$qryEmpList .= "AND empSssNo LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}
	
	if($_GET['srchType'] == 4){
		$qryEmpList .= "AND empBrnCode LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
	}	
}
$qryEmpList .=	"ORDER BY empLastName limit $intOffset,$intLimit";

$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);


?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="3" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;BlackListed Employees
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					
                    <tr>
                        <td colspan="6" class="gridToolbar">
                        	<?php if ($_SESSION['user_level'] != 3) {						?>
                        
                        <a href="#" onclick="location.href='blacklisted_list_add_ajax.php?act=Add'" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Blacklist Employee</a> |
						
						<?php } ?>
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
                            <INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('blacklisted_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
                        </td>
                    </tr>
					<tr>
						<td width="3%" class="gridDtlLbl" align="center">#</td>
						<td width="10%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="23%" class="gridDtlLbl" align="center">NAME</td>
                        <td width="40%" class="gridDtlLbl" align="center">REASON</td>
                        <td width="15%" class="gridDtlLbl" align="center">BRANCH</td>
						
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
							
							//Check the Employee if he/she has record in payroll_company..tblBlacklistedEmp
							$res_noOfRecords = $blackListObj->chkEmpNoRecords($empListVal['blacklist_No']);
							$noOfRecords = $blackListObj->getRecCount($res_noOfRecords);
							if($noOfRecords>=1)
								$visibility = "";
							else
								$visibility = "hidden";
					?>
					
                    <tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$empListVal['blacklist_No']?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
                        <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("�","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
						<td class="gridDtlVal"><font color="#FF0000" class="gridDtlLblTxt"><?php echo $empListVal['reason']; ?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?php echo $empListVal['empBrnCode']; ?></font></td>
						   <td class="gridDtlVal" >
							<table border="0" width="70%" align="center">
                            	<tr align="center" >
                                	
                                	
                                	  
                                    	<td style="visibility:<?php echo $visibility; ?>">
                                            <a href="#"  onClick="blackList_Pop('<?=$empListVal['blacklist_No']?>','v');"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="View BlackList Information"></a>                                    
                                       </td>
                                        <?
                                        if ($_SESSION['user_level'] == 1){
										?>
                                    	<td style="visibility:<?php echo $visibility; ?>">
                                            <a href="#"  onClick="blackList_Pop('<?=$empListVal['blacklist_No']?>','e');"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Employee BlackList Information"></a>                                    
                                        </td>
                                        
                                       <td class="gridDtlVal" align="left">
                                            <a href="#" onClick="del_Blacklist('<?=$empListVal['blacklist_No']?>');"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Set the Employee as BlackListed."></a>                                    
                                        </td>
                                     	<?
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