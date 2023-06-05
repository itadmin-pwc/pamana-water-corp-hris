<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
/*if ($_SESSION['user_level'] == 3) {
	$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
	
	if($userinfo['empLocCode']=='0001'){
		$brnCode = " $and empLocCode='".$userinfo['empLocCode']."'";
		$brnCodelist = " AND  empLocCode='".$userinfo['empLocCode']."'";
	}
	
	
	else{
		if($userinfo['empLocCode']!='0001'){
			$brnCode = " $and empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCodelist = " AND empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCode_View = 1;
		}
	}
}*/
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');

if(isset($_GET['btnSearchReg'])){
	//$viewRegNoCustNo = "and cust.custNo is null";	
	echo "alert('nhomer');";
}

if($brnCode_View ==""){
	$arrBrnch = $common->makeArr($common->getBranchByCompGrp(" and brnDefGrp='".$_SESSION["pay_group"]."' and compCode='".$_SESSION["company_code"]."'"),'brnCode','brnDesc','All');
}
$empStat = " AND empStat NOT IN('RS','IN','TR') and employmentTag='RG' $viewRegNoCustNo";
$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}' 
				 AND empPayGrp<>''
				 $empStat
				 and empPayCat<>0 ";
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

$qryEmpList = "SELECT TOP $intLimit emp.empNo,empLastName,empFirstName,empMidName,custNo
		FROM tblEmpMast emp left join tblCustomerNo cust on emp.empNo=cust.empNo
		WHERE  empPayCat<>0 $empStat and empPayCat<>''
		AND empPayGrp<>'' 
		and
		emp.empNo NOT IN
        (SELECT TOP $intOffset empNo FROM tblEmpMast WHERE  empPayGrp<>'' $empStat and empPayCat<>'' and compCode = '{$sessionVars['compCode']}' and empPayCat<>0 $brnCodelist "; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND emp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
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
$qryEmpList .= " ORDER BY empLastName) 
				AND emp.compCode = '{$sessionVars['compCode']}' ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND emp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
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

$sqlEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($sqlEmpList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Customer Master List</td>
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
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$srchType,'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('customer_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')"></br>
					  <input type="button" name="btnSearchReg" id="btnSearchReg" value="Regular Employees with out Customer No."  class="inputs" onclick="showRegEmp('customer_list_allRegEmp.php?act=Add&empNo=<?=$empListVal['empNo']?>&custNo=<?=$empListVal['custNo']?>','List of Regular Employees w/o Customer Nnumber','','customer_list_ajax.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
					  <input type="button" name="btnSearchTrans" id="btnSearchTrans" value="Transferred Employees"  class="inputs" onclick="showTransEmp('transferred_employees.php?act=Add&amp;empNo=<?=$empListVal['empNo']?>&amp;custNo=<?=$empListVal['custNo']?>','Transferred Employees','','customer_list_ajax.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" /></td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="50%" class="gridDtlLbl" align="center">NAME</td>
						<td width="20%" class="gridDtlLbl" align="center">CUSTOMER NO.</td>
						<td class="gridDtlLbl" align="center" colspan="4">ACTION</td>
					</tr>
					<? 
					if($common->getRecCount($sqlEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("Ñ","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['custNo']?></font></td>
						<td class="gridDtlVal" align="center">
							<a href="#" onClick="PopUp('customer_act.php?act=Add&empNo=<?=$empListVal['empNo']?>&custNo=<?=$empListVal['custNo']?>','ADD CUSTOMER NO.','','customer_list_ajax.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/allowance_list.png" title="View/Edit" /></a>
                            
							
                        </td> 
						
					</tr>
					<tr id="trPrevEmpCont<?=$empListVal['empNo']?>" style="display:none;">
						<td colspan="7" >
							
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
							<? $pager->_viewPagerButton("customer_list_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>
