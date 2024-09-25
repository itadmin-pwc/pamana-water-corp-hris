<?
	/*
		Date Created	:	08032010
		Created By		:	Genarra Arong
	*/

	session_start();
	include("../../../includes/userErrorHandler.php");
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("transaction_obj.php");

	$common = new commonObj();
	$emptimesheetObj = new transactionObj();
	$sessionVars = $emptimesheetObj->getSeesionVars();
	$emptimesheetObj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(20,'../../../images/');
	$sessionVars = $common->getSeesionVars();


	$url = $_GET["url"];
	
	if ($_SESSION['user_level'] == 3)
	{
		$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		$brnCodelist = " AND empMast.empNo<>'".$_SESSION['employee_number']."' and empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')";
	}
	
	$array_userPayCat = explode(',', $_SESSION['user_payCat']);
	
	if(in_array(9,$array_userPayCat))
	{
		$where_empStat = "";
	}
	else
	{
		$where_empStat = " AND empStat NOT IN('RS','IN','TR')";
	}
	
	$user_payCat_view = " AND empPayCat IN ({$_SESSION['user_payCat']})";


	//variable declaration 
	$preEmplyrVal =0;
	$srchType = 0;
	
	
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	
	if($brnCode_View ==""){
		$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblTK_UserBranch tblUB, tblBranch as tblbrn
							where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'  AND tblUB.processTag='Y'
							order by brnDesc";
		
		$resBrnches = $common->execQry($queryBrnches);
		$arrBrnches = $common->getArrRes($resBrnches);
		$arrBrnch = $common->makeArr($arrBrnches,'brnCode','brnDesc','All');
	}
	
	if($url == 'processing')
	{
		$qryIntMaxRec = "SELECT     empTimeSheet.empNo, empMast.empLastName, empMast.empFirstName, empMast.empMidName, COUNT(empTimeSheet.checkTag) AS checkTag
						 FROM tblTK_Timesheet empTimeSheet INNER JOIN
							  tblEmpMast empMast ON empTimeSheet.empNo = empMast.empNo
						  WHERE (empTimeSheet.checkTag in('C','Y')) AND (empTimeSheet.compcode = '".$_SESSION["company_code"]."') AND (empMast.compCode = '".$_SESSION["company_code"]."')
						  $brnCodelist $where_empStat and empPayCat<>0 $user_payCat_view";
	}
	else
	{
		$qryIntMaxRec = "SELECT * FROM tblEmpMast empMast
						 WHERE compCode = '{$sessionVars['compCode']}' 
						 $brnCodelist $where_empStat
						 and empPayCat<>0 $user_payCat_view";
	}				
			if($_GET['isSearch'] == 1){
				if($_GET['srchType'] == 0){
					$qryIntMaxRec .= "AND empMast.empNo LIKE '{$_GET['txtSrch']}%' ";
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
			
			
	$qryIntMaxRec.= " ".($url=='processing'?" GROUP BY empTimeSheet.empNo, empMast.empLastName, empMast.empFirstName, empMast.empMidName":" ")."  
					ORDER BY empMast.empLastName, empMast.empFirstName, empMast.empMidName";
	
	//echo $qryIntMaxRec."<br><br>";
	
	$resIntMaxRec = $common->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	if($url == 'processing')
	{
	$qryEmpList = "SELECT  empTimeSheet.empNo, empMast.empLastName, empMast.empFirstName, empMast.empMidName, COUNT(empTimeSheet.checkTag) AS checkTag
						 FROM tblTK_Timesheet empTimeSheet INNER JOIN
							  tblEmpMast empMast ON empTimeSheet.empNo = empMast.empNo
						  WHERE (empTimeSheet.checkTag in('C','Y')) AND (empTimeSheet.compcode = '".$_SESSION["company_code"]."') AND (empMast.compCode = '".$_SESSION["company_code"]."')
						  and empPayCat<>0 $where_empStat $user_payCat_view $brnCodelist ";
	}
	else
	{
	$qryEmpList = "SELECT 
					FROM tblEmpMast empMast
					WHERE compCode= '{$sessionVars['compCode']}'
					and empPayCat<>0 $where_empStat $user_payCat_view  $brnCodelist "; 
	}
					if($_GET['isSearch'] == 1){
						if($_GET['srchType'] == 0){
							$qryEmpList .= "AND empMast.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
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
	$qryEmpList .=" ".($url=='processing'?" GROUP BY empTimeSheet.empNo,  empMast.empLastName, empMast.empFirstName, empMast.empMidName ":" ")."
					ORDER BY empMast.empLastName, empMast.empFirstName, empMast.empMidName LIMIT $intOffset,$intLimit";
	
	//echo $qryEmpList;
	$resEmpList = $common->execQry($qryEmpList);
	$arrEmpList = $common->getArrRes($resEmpList);
	
?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee View / Edit Time Sheet
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="7" class="gridToolbar">
                         <FONT class="ToolBarseparator">&nbsp;</font>
						<?
							if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
								if(isset($_GET['srchType']) ){ 
									$srchType = $_GET['srchType'];
								}
							}
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('view_edit_employee_timesheet_listAjaxResult.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&url=<?=$url?>&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
						
					</td>
					
                    <tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<?
							if($url=='processing')
							{
						?>
                        		<td width="50%" class="gridDtlLbl" align="center">NAME</td>
                       			<td width="20%" class="gridDtlLbl" align="center">CHECKTAG</td>
                        <?
							}
							else
							{
						?>
                    			<td width="70%" class="gridDtlLbl" align="center">CHECKTAG</td>
                        <?
                        	}
						?>
                   
						<td class="gridDtlLbl" align="center">ACTION</td>
					</tr>
				
                	<?
					if($common->getRecCount($resEmpList) > 0)
					{
						$i=0;
						foreach ($arrEmpList as $empListVal)
						{
							$disabledButtons = "";
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
							. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
							
							
					?>
                            <tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                <td class="gridDtlVal"><?=$i?></td>
                                <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
                                <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("�","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
                              	<?
									if($url=='processing')
									{
								?>
                                <td class="gridDtlVal" align="center">
                                    	<?=$empListVal['checkTag']?>
                                </td>
                              	
                                <?
									}
								?>
                                <td class="gridDtlVal" align="center">
                                	<a href="#" onClick="location.href='employee_timesheet.php?empNo=<?=$empListVal['empNo']?>&url=<?=($url!=""?$url:"view")?>'"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Employee Time Sheet"></a>
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
                        	<? $pager->_viewPagerButton("view_edit_employee_timesheet_listAjaxResult.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"].'&url='.$url,'');?>
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