<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');


if($brnCode_View ==""){
	$arrBrnch = $common->makeArr($common->getBranchByCompGrp(" and brnDefGrp='".$_SESSION["pay_group"]."' and compCode='".$_SESSION["company_code"]."'"),'brnCode','brnDesc','All');
}
$empStat = " AND empStat NOT IN('RS','IN','TR','USER') and employmentTag='RG'";

$qryIntMaxRec = "SELECT * FROM tblEmpMast 
				 LEFT JOIN tblCustomerNo  on tblEmpMast.empNo=tblCustomerNo.empNo 
			     WHERE compCode = '{$sessionVars['compCode']}' 
				 AND tblCustomerNo.custNo is null
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
        }

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$qryEmpList = "SELECT emp.empNo, emp.empLastName, emp.empFirstName, emp.empMidName, 
		cust.custNo, CONVERT(varchar(12),emp.dateReg,107) as dateReg, brn.brnShortDesc 
		FROM tblEmpMast emp 
		LEFT OUTER JOIN tblCustomerNo cust on emp.empNo=cust.empNo
		INNER JOIN tblBranch brn on emp.empBrnCode=brn.brnCode
		WHERE  empPayCat<>0
		AND cust.custNo is null
		$empStat 
		AND empPayCat<>''	
		AND empPayGrp<>'' 
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
        }
		
$qryEmpList .= " Limit $intOffset,$intLimit";
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
					<td colspan="9" class="gridToolbar">
						
                         <FONT class="ToolBarseparator">&nbsp;</font>
						<?
					
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('customer_list_AllRegEmp_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','','','../../../images/')"></td>
					<tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="16%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="39%" class="gridDtlLbl" align="center">NAME</td>
						<td width="22%" class="gridDtlLbl" align="center">STORE LOCATION</td>
						<td width="13%" class="gridDtlLbl" align="center">DATE REGULARIZED</td>
						<td width="8%" colspan="4" align="center" class="gridDtlLbl">ACTION</td>
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
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
						  <?=$empListVal['empNo']?>
						</font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("Ñ","&Ntilde;",htmlentities($empListVal['empLastName']). ", " . htmlentities($empListVal['empFirstName']) ." ". htmlentities($empListVal['empMidName']));?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
						  <?=$empListVal['brnShortDesc']?>
						</font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['dateReg']?></font></td>
						<td class="gridDtlVal" align="center">
						  <a href="#" onClick="PopUp('customer_act.php?act=Add&empNo=<?=$empListVal['empNo']?>&custNo=<?=$empListVal['custNo']?>','ADD CUSTOMER NO.','','customer_list_AllRegEmp_ajax.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/allowance_list.png" title="Add Customer Number" /></a>
                            
							
                        </td> 
						
					</tr>
					<tr id="trPrevEmpCont<?=$empListVal['empNo']?>" style="display:none;">
						<td colspan="9" >
							
						</td>
					</tr>
					<?
						}
					}
					else{
					?>
					<tr>
						<td colspan="9" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="9" align="center" class="" style="visibility:hidden">
							<? $pager->_viewPagerButton("customer_list_AllRegEmp_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
</HTML>

<?$common->disConnect();?>
